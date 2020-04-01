<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Client\Http\Controllers;

use Illuminate\Support\Collection;
use Myth\Api\Client\Entities\ApiClientRelation;
use Myth\Api\Client\Http\Resources\CollectionResponse;
use Myth\Api\Client\Utilities\MythApiClientWrapper as ClientWrapper;

class ClientApiController extends BaseController{

    public function index(ClientWrapper $router){
        // dd($router);
        // dd($router->getModel(),$router->getModelName(),$router->getModelUri());

        // $query = $router->getModel()->TamweelkIndex($this->request, $router, $this)->TamweelkScope(
        //     $this->request->get('sync', 'sync')
        // );
        $query = $router->query()->MythApiIndexScope($this->request, $router, $this)->MythApiScope(
            $this->request->get('sync', 'sync')
        );
        // dd($router->getModel()->myth_api_data);
        ($this->itemsPerPage === -1) && ($this->itemsPerPage = $query->count());
        $query = $query->paginate((int) $this->itemsPerPage, '*', 'page', (int) $this->page);
        return new CollectionResponse($query);
    }

    /**
     * @param ClientWrapper $router
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(ClientWrapper $router){
        /** validation on primary key */
        $this->validateManagerPrimaryKey();
        /** @var $model */
        $model = $router->getModel();
        $model->setRawAttributes($model->getMythApiFillable($this->request));

        $validation = $model->mythApiValidation($this->request, $this);
        if(!$validation->getSuccess()){
            return $router->jsonResponse($validation->getData(), $validation->getMessage(), 422);
        }

        $model->mythApiClientSaving($this->request);

        if(!$this->debugMode && $model->save()){
            $model->createSyncData($this->getManagerPrimaryKey(), $router->getManagerName());
            $model->refresh();
            $model->mythApiClientSaved($this->request);
        }

        return $model->toJsonResponse();
    }

    /**
     * @param ClientWrapper $router
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Myth\Api\Client\Exceptions\ManagerNotFountException
     */
    public function update(ClientWrapper $router){
        /** validation on primary key */
        // $this->validateManagerPrimaryKey();
        $this->validate(
            $this->request,
            [
                "{$this->primaryKey}"                      => ["array"],
                "{$this->primaryKey}.*"                    => ["required_with:{$this->primaryKey}", "integer"],
                "update_client_data"                       => ["array"],
                "update_client_data.*.myth_api_manager_id" => [
                    "required_with:update_client_data",
                    "integer",
                ],
                "update_client_data.*.data"                => ["required_with:update_client_data", "array"],
            ]
        );
        $ids = (new Collection($this->getManagerPrimaryKey()))->unique()->values()->toArray();
        $models = new Collection($this->request->get("update_client_data", []));
        $updated = [];
        $modelsUpdated = [];
        $router->manager()->MustSync()->where('syncable_type', '=', $router->getModelName())->whereIn(
            $this->primaryKey,
            $ids
        )->get()->each(
            function(
                ApiClientRelation $entity
            ) use (&$updated){
                if(!$this->debugMode){
                    $entity->setSynced();
                }
                $updated[] = $entity->myth_api_manager_id;
            }
        );

        if($models->count()){
            $models->each(
                function($row) use ($router, &$modelsUpdated){
                    $model = $router->manager()->where('syncable_type', '=', $router->getModelName())->where(
                        $this->primaryKey,
                        $row['myth_api_manager_id']
                    )->first();
                    if($model){
                        $m = $model->syncable;
                        if(!$this->debugMode && $m->mythApiUpdateModelClientData($this->request, $row['data'])){
                            $m->update();
                        }
                        $m && ($modelsUpdated[] = $row['myth_api_manager_id']);
                    }
                }
            );
        }
        return $router->jsonResponse(
            [
                "manger_models" => $updated,
                "client_models" => $modelsUpdated,
            ],
            "Updated Data: ".count($updated)
        );
    }
}
