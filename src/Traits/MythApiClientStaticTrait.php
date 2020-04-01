<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Client\Traits;

use Illuminate\Support\Str;

/**
 * Trait MythApiClientStaticTrait
 * static trait dont override this methods
 * @package Myth\Api\Client\Traits
 */
trait MythApiClientStaticTrait{

    /**
     * @Don't change
     * append sync data to response
     * @return array
     */
    public function appendToMythApiArray(): array{
        $data = $this->toMythApiArray();
        $attributes = $this->getAttributes();
        foreach($attributes as $column => $v){
            if(!array_key_exists($column, $data)){
                $data[$column] = $this->{$column};
            }
        }
        $data['myth_api_data'] = $this->getSyncDataAsArray();
        return (array) $data;
    }

    /**
     * @Don't change
     * append relations to api data array for model
     * @param array|null $data
     * @return array
     */
    public function appendRelations(array $data = null): array{
        is_null($data) && ($data = $this->toMythApiArray());
        foreach($data as $key => $value){
            $column = trim(is_numeric($key) && is_string($value) ? $value : $key);

            if($this->hasMythApiMapRelation($column)){
                $data["relation_{$column}"] = $this->getFromMythApiMapRelation($column);
            }
            elseif(Str::endsWith($column, ($n = '_id')) && ($relation = Str::before($column, $n))){
                $data["relation_{$column}"] = $this->{$relation};
            }
        }
        return $data;
    }
}