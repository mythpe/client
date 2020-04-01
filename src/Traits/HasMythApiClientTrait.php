<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Client\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Myth\Api\Client\Facades\Client;
use Myth\Api\Client\Http\Resources\ResponseJsonResource;

/**
 * Trait HasMythApiClientTrait
 * @package Myth\Api\Client\Traits
 */
trait HasMythApiClientTrait{

    use MythApiClientRelationTrait;
    use MythApiClientStaticTrait;
    use MythApiClientScopesTrait;
    use MythApiClientAbstractTrait;
    use MythApiClientOverrideTrait;

    /**
     *
     */
    protected static function bootHasMythApiClientTrait(){
        static::saved(
            function($model){
                $model->setMustSync();
            }
        );
        static::deleted(
            function($model){
                $model->myth_api_data()->delete();
            }
        );
    }

    /**
     * Can override
     * @return string
     */
    public function getMythApiRouteName(): string{
        return Str::singular(strtolower(class_basename(static::class)));
    }

    /**
     * Convert Model to json response
     * @param string $message
     * @param int $status
     * @param array $headers
     * @param int $options
     * @return \Illuminate\Http\JsonResponse
     */
    public function toJsonResponse($message = '', $status = 200, array $headers = [], $options = 0): JsonResponse{
        return Client::JsonResponse(new ResponseJsonResource($this), $message, $status, $headers, $options);
    }
}