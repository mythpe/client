<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Client\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Myth\Api\Client\Entities\ApiClientRelation;
use Myth\Api\Client\Facades\Client;
use Myth\Api\Client\Traits\HasMythApiClientTrait;

/**
 * Class BaseController
 * @package Myth\Api\Client\Http\Controllers
 */
class BaseController extends Controller{

    use ValidatesRequests;

    /** @var int */
    protected $page;
    /** @var int */
    protected $itemsPerPage;
    /** @var Request */
    protected $request;
    /** @var String */
    protected $primaryKey;
    /** @var bool */
    protected $debugMode = false;

    /**
     * BaseController constructor.
     * @param Request $request
     */
    public function __construct(Request $request){
        $this->itemsPerPage = intval($request->get("itemsPerPage", 15));
        $this->page = intval($request->get("page", 1)) ? : 1;
        $this->request = $request;
        $this->primaryKey = Client::getManagerPrimaryKeyFromRequest();
        // $this->debugMode = (boolean) (config('app.debug') || $this->request->get("debug", false));
        $this->debugMode = (boolean) ($this->request->get("debug", false));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexOf(){
        $t = [];
        try{
            $f = DB::select('SHOW TABLES');
            $facade = collect(json_decode(collect($f)->toJson(), true))->map(
                function($v){
                    return is_array($v) ? current($v) : $v;
                }
            );
            foreach($facade as $a){
                $t[] = $a;
            }
        }
        catch(\Exception $exception){
            $t = [];
        }
        return Client::jsonResponse($t);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexOfSchema(){
        $response = [];
        try{
            $response = Schema::getColumnListing($this->request->post('key'));
        }
        catch(Exception $exception){
            $response = [];
        }
        return Client::jsonResponse($response);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexOfSchemaData(){
        try{
            $response = new class extends Model{

                use HasMythApiClientTrait;
                protected $table;

                public function __construct(array $attributes = []){
                    parent::__construct($attributes);
                    $this->table = request()->post('key');
                }
            };
            $response = $response->all()->toArray();
        }
        catch(Exception $exception){
            $response = [];
        }
        return Client::jsonResponse($response);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexOfEntries(){
        $response = [];
        try{
            $response = Client::getEntities();
        }
        catch(Exception $exception){
            $response = [];
        }
        return Client::jsonResponse($response);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function storeSchema(){
        /** validation on tamweelk primary key */
        $this->validateManagerPrimaryKey();
        $model = new class extends Model{

            use HasMythApiClientTrait;
            protected $table;

            public function __construct(array $attributes = []){
                parent::__construct($attributes);
                $this->table = request()->get('key');
            }
        };
        $this->validate(
            $this->request,
            [
                'data'                    => ['required', 'array'],
                'sync_data'               => ['required', 'array'],
                'sync_data.syncable_type' => ['required'],
            ]
        );
        $model->setRawAttributes($this->request->get('data'));
        !$this->debugMode && $model->save();
        $sync_data = [
            Client::getManagerPrimaryKeyFromRequest() => $this->getManagerPrimaryKey(),
            "manager_name"                            => Client::getManagerName(),
            "must_sync"                               => true,
            "syncable_id"                             => $model->id,
            "syncable_type"                           => $this->request->get('sync_data', [])['syncable_type'],
        ];
        $sync = ApiClientRelation::newModelInstance()->setRawAttributes($sync_data);
        if(!$this->debugMode){
            $sync->save();
            $model->refresh();
            $sync->refresh();
        }
        $data = array_merge(
            $model->appendRelations($model->appendToMythApiArray()),
            [
                'myth_api_data' => $sync->toArray(),
            ]
        );
        return Client::jsonResponse($data);
    }

    /**
     * @return mixed
     */
    public function getManagerPrimaryKey(){
        return ($r = $this->request->get($this->primaryKey, 0)) ? $r : null;
    }

    /**
     * @throws ValidationException
     */
    protected function validateManagerPrimaryKey(){
        $this->validate(
            $this->request,
            [
                $this->primaryKey => ["required"],
            ],
            [
                "required" => "Primary key is required. ".$this->primaryKey,
            ]
        );
    }
}
