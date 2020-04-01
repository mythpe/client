<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Client\Traits;

use Carbon\Carbon;
use Myth\Api\Client\Entities\ApiClientRelation;
use Myth\Api\Client\Facades\Client;

/**
 * Trait MythApiClientRelationTrait
 * @property-read \Myth\Api\Client\Entities\ApiClientRelation myth_api_data
 * @package Myth\Api\Client\Traits
 */
trait MythApiClientRelationTrait{

    /**
     * map of relations
     * array[attribute_name => method_relation_name]
     * @var array
     */
    protected $mythApiMapRelations = [];

    /**
     * set model must sync next time
     * @return $this
     */
    public function setMustSync(){
        if(!$this->myth_api_data()->exists()) return $this;
        $this->myth_api_data()->update(['must_sync' => true]);
        return $this;
    }

    /**
     * Morph relation
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function myth_api_data(){
        return $this->morphOne(ApiClientRelation::class, 'syncable');
    }

    /**
     * Set Model synced
     * @return $this
     */
    public function setSynced(){
        if(!$this->myth_api_data()->exists()) return $this;
        $this->myth_api_data()->update(['must_sync' => false, 'last_sync_time' => Carbon::now()]);
        return $this;
    }

    /**
     * Get morph as array
     * @return array
     */
    public function getSyncDataAsArray(): array{
        return (array) ($m = $this->myth_api_data) ? $m->toarray() : [];
    }

    /**
     * @param $manager_id
     * @param $manager_name
     * @return $this
     */
    public function createSyncData($manager_id, $manager_name){
        $primaryKey = Client::getManagerPrimaryKeyFromRequest();
        ApiClientRelation::query()->whereSyncableType(static::class)->where('myth_api_manager_id', $manager_id)->delete(
        );
        $this->myth_api_data()->create(
            [
                "must_sync"           => true,
                "myth_api_manager_id" => (int) $manager_id,
                "manager_name"        => (string) $manager_name,
            ]
        );
        return $this;
    }

    /**
     * @param $column
     * @return bool
     */
    protected function hasMythApiMapRelation($column){
        return array_key_exists($column, $this->mythApiMapRelations);
    }

    /**
     * @param $column
     * @return mixed|null
     */
    protected function getFromMythApiMapRelation($column){
        if($this->hasMythApiMapRelation($column)){
            if(($relation = $this->mythApiMapRelations[$column])){
                if(method_exists($this, $relation)){
                    return ($r = $this->{$relation}) ? $r : $this->{$relation}();
                }
                return $this->{$relation};
            }
        }
        return null;
    }
}