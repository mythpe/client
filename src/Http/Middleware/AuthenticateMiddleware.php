<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Client\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Myth\Api\Client\Facades\Client;

/**
 * Class AuthenticateMiddleware
 * @package Myth\Api\Client\Http\Middleware
 */
class AuthenticateMiddleware{

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $request->headers->set('Accept', 'application/json');
        Client::resolveClientConnection($request);
        return $next($request);
    }
}
