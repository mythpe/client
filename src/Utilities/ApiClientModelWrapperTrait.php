<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Client\Utilities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Myth\Api\Client\Entities\ApiClientRelation;
use Myth\Api\Client\Exceptions\ManagerNotFountException;

/**
 * Trait ApiClientModelWrapperTrait
 * @package Myth\Api\Client\Utilities
 */
trait ApiClientModelWrapperTrait{

    /**
     * @var
     */
    protected $model;
    /**
     * @var
     */
    protected $modelName;
    /**
     * @var
     */
    protected $modelUri;

    /**
     * @return mixed
     */
    public function getModelUri(){
        return $this->modelUri;
    }

    /**
     * @param $modelUri
     * @return $this
     */
    public function setModelUri($modelUri){
        $this->modelUri = $modelUri;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getModelName(){
        return $this->modelName;
    }

    /**
     * @param $modelName
     * @return $this
     */
    public function setModelName($modelName){
        $this->modelName = $modelName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getModel(){
        return $this->model;
    }

    /**
     * @param $model
     * @return $this
     */
    public function setModel($model){
        $this->model = $model;
        return $this;
    }

    /**
     * @return string
     */
    public function getModelBasename(): string{
        return class_basename($this->modelName);
    }

    /**
     * @return Builder
     */
    public function getClientEntryBuilder(): Builder{
        return ApiClientRelation::query()->whereSyncableType($this->modelName);
    }

    /**
     * @return Collection
     */
    public function getClientEntries(): Collection{
        return $this->getClientEntryBuilder()->get();
    }

    /**
     * @param null $managerName
     * @return Builder
     * @throws ManagerNotFountException
     */
    public function manager($managerName = null){
        is_null($managerName) && ($managerName = $this->getManagerName());
        if(!$managerName) throw new ManagerNotFountException();
        return ApiClientRelation::ByManager($managerName);
    }

    /**
     * @return Builder
     */
    public function query(): Builder{
        return $this->getModel()->query();
    }
}