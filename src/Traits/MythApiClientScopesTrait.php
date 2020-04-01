<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Client\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Myth\Api\Client\Http\Controllers\BaseController;
use Myth\Api\Client\Utilities\MythApiClientWrapper;

/**
 * Trait MythApiClientScopesTrait
 * @method static Builder MythApiScope
 * @method static Builder MythApiIndexScope
 * @package Myth\Api\Client\Traits
 */
trait MythApiClientScopesTrait{

    /**
     * Get All Manager Data
     * @param Builder $builder
     * @param null $syncType
     * @return Builder
     */
    public function scopeMythApiScope(Builder $builder, $syncType = null): Builder{
        $sync = null;
        switch($syncType){
        case 'synced':
            $sync = false;
        break;
        case 'sync':
            $sync = true;
        break;

        case 'all':
            $sync = null;
        break;
        }
        return $builder->whereHas(
            'myth_api_data',
            function(Builder $builder) use ($sync){
                if(is_null($sync)) return $builder;
                return $builder->where('must_sync', $sync);
            }
        );
    }

    /**
     * Entity index scope
     * You can override this method to customize your query
     * @param Builder $builder
     * @param MythApiClientWrapper $model
     * @param Request $request
     * @param BaseController $controller
     * @param mixed ...$args
     * @return Builder
     */
    public function scopeMythApiIndexScope(
        Builder $builder,
        Request $request,
        MythApiClientWrapper $model,
        BaseController $controller,
        ...$args
    ): Builder{
        return $builder;
    }

}