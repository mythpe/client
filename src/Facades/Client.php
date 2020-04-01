<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Client\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Client
 * @method static string getHeaderKey
 * @method static string getSecret
 * @method static string resolveRouteBinding
 * @method static string makeSecret
 * @method static \Illuminate\Http\JsonResponse jsonResponse
 * @package Myth\Api\Client\Facades
 */
class Client extends Facade{

    /**
     * Get the registered name of the component.
     */
    public static function getFacadeAccessor(){ return 'myth.api.client'; }
}
