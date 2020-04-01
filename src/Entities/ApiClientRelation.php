<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Client\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ApiClientRelation
 * @method static Builder ByManager(string $managerName)
 * @method static \Illuminate\Database\Eloquent\Model|static newModelInstance
 * @package Myth\Api\Client\Entities
 */
class ApiClientRelation extends Model{

    /** @var string */
    protected $table = 'myth_api_client_entries';
    /** @var array */
    protected $fillable = [
        "syncable_id",
        "syncable_type",
        "myth_api_manager_id",
        "manager_name",
        "must_sync",
        "las_sync_time",
    ];
    /** @var array */
    protected $attributes = [
        "myth_api_manager_id" => 0,
        "must_sync"           => true,
    ];
    /** @var array */
    protected $casts = [
        "myth_api_manager_id" => "integer",
        "must_sync"           => "boolean",
    ];

    /**
     * @param Builder $builder
     * @param $managerName
     * @return Builder
     */
    public function scopeByManager(Builder $builder, $managerName){
        return $builder->where('manager_name', 'LIKE', $managerName);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function syncable(){
        return $this->morphTo();
    }

    /**
     * @return $this
     */
    public function setSynced(){
        $this->must_sync = false;
        $this->las_sync_time = Carbon::now();
        $this->save();
        return $this;
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeMustSync(Builder $builder){
        return $builder->where('must_sync', true);
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeWasSynced(Builder $builder){
        return $builder->where('must_sync', false);
    }
}