<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Client\Utilities;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Myth\Api\Client\Exceptions\ErrorClientTokenException;
use Myth\Api\Client\Exceptions\HeaderManagerKeyException;
use Myth\Api\Client\Exceptions\HeaderSecretKeyException;
use Myth\Api\Client\Exceptions\ManagerNotFountException;
use Myth\Api\Client\Exceptions\NotFoundEntityException;
use Myth\Api\Client\Exceptions\SecretNotSetupException;

/**
 * Trait ApiClientHttpWrapperTrait
 * @package Myth\Api\Client\Utilities
 */
trait ApiClientHttpWrapperTrait{

    /** @var array $middleware array for middleware */
    protected $middleware = ['myth.auth.client', 'api'];
    /** @var string $routeName name of client routes */
    protected $routeName = 'myth::';
    /** @var string $routePrefix route prefix of client connection */
    protected $routePrefix = 'myth';
    /** @var string $headerKey secret header key name */
    protected $headerKey = 'MYTH-SRF';
    /** @var string $headerManagerKeyName Manager header key name */
    protected $headerManagerKeyName = 'MYTH-NAME';
    /** @var string $managerPrimaryKeyFromRequest primary key for manager must be on every request same database relation */
    protected $managerPrimaryKeyFromRequest = 'myth_api_manager_id';

    /**
     * @return string
     */
    public function getManagerPrimaryKeyFromRequest(): string{
        return $this->managerPrimaryKeyFromRequest;
    }

    /**
     * @return string
     */
    public function getHeaderKey(): string{
        return $this->headerKey;
    }

    /**
     * @param array $options
     */
    public function routes($options = []): void{
        // dd(231);
        Route::group(
            $options,
            function(Router $router){
                $router->group(
                    [
                        'middleware' => $this->getMiddleware(),
                        'as'         => $this->getRouteName(),
                        'prefix'     => $this->getRoutePrefix(),
                    ],
                    function(Router $router){
                        $controller = "\\Myth\\Api\\Client\\Http\\Controllers\\ClientApiController";
                        $router->get('index/{MythApiModelClient}', "{$controller}@index")->name('index');
                        $router->post('store/{MythApiModelClient}', "{$controller}@store")->name('store');
                        $router->put('update/{MythApiModelClient}', "{$controller}@update")->name('update');
                        $router->post('index-of', "{$controller}@indexOf")->name('indexOf');
                        $router->post('index-of-schema', "{$controller}@indexOfSchema")->name('indexOfSchema');
                        $router->post('index-of-schema-data', "{$controller}@indexOfSchemaData")->name('schemaData');
                        $router->get('index-of-entries', "{$controller}@indexOfEntries")->name('indexOfEntries');
                        $router->post('store-schema', "{$controller}@storeSchema")->name('storeSchema');
                    }
                );
            }
        );
    }

    /**
     * @return string
     */
    public function getMiddleware(): array{
        return $this->middleware;
    }

    /**
     * @return string
     */
    public function getRouteName(): string{
        return $this->routeName;
    }

    /**
     * @return string
     */
    public function getRoutePrefix(): string{
        return $this->routePrefix;
    }

    /**
     * Return static json response
     * @param array $data
     * @param string $message
     * @param int $status
     * @param array $headers
     * @param int $options
     * @return \Illuminate\Http\JsonResponse
     */
    public function jsonResponse(
        $data = [],
        $message = '',
        $status = 200,
        array $headers = [],
        $options = 0
    ): JsonResponse{
        $success = null;
        if(func_num_args() === 1 && is_array($data)){
            if(isset($data['message'])){
                $message = $data['message'];
                unset($data['message']);
            }
            if(isset($data['success'])){
                $success = $data['success'];
                unset($data['success']);
            }
            if(isset($data['status'])){
                $status = $data['status'];
                unset($data['status']);
            }
            if(isset($data['options'])){
                $options = $data['options'];
                unset($data['options']);
            }
            if(isset($data['headers'])){
                $headers = $data['headers'];
                unset($data['headers']);
            }
            if(isset($data['data'])){
                $data = $data['data'];
                unset($data['data']);
            }
        }

        $json = [
            "message" => (string) $message,
            "success" => (boolean) (is_null($success) ? ((int) $status === 200) : $success),
            "data"    => $data,
        ];

        $factory = app(ResponseFactory::class);
        return $factory->json($json, $status, $headers, $options)->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param $value
     * @return $this
     * @throws ErrorClientTokenException
     * @throws HeaderSecretKeyException
     * @throws NotFoundEntityException
     * @throws SecretNotSetupException
     */
    public function resolveRouteBinding($value){
        $this->resolveClientConnection(request());
        $appModel = null;
        // dd($this->getEntities());
        foreach($this->getEntities() as $entity => $bind){
            $modelName = is_numeric($entity) ? $bind : $entity;
            $appModel = $this->app->make($modelName);
            $uri = $appModel ? $appModel->getMythApiRouteName() : $bind;
            if($value === $uri){
                $this->setModel($appModel);
                $this->setModelName(get_class($appModel));
                $this->setModelUri($uri);
                break;
            }
            $appModel = null;
            $modelName = null;
        }
        if(!$appModel || !$modelName) throw new NotFoundEntityException('Entity Not Found');
        return $this;
    }

    /**
     * @param Request $request
     * @throws HeaderManagerKeyException
     * @throws ManagerNotFountException
     */
    public function resolveManager(Request $request){
        $managerKey = $this->headerManagerKeyName;
        if(!$request->hasHeader($managerKey)){
            throw new HeaderManagerKeyException('Manager name not provided');
        }
        $managerName = preg_replace('/\s+/', '', trim($request->header($managerKey)));
        if(!$this->hasManager($managerName)){
            throw new ManagerNotFountException('Manager not found');
        }
        $this->setManagerName($managerName);
    }

    /**
     * @param Request $request
     * @throws ErrorClientTokenException
     * @throws HeaderManagerKeyException
     * @throws HeaderSecretKeyException
     * @throws ManagerNotFountException
     * @throws SecretNotSetupException
     */
    public function resolveClientConnection(Request $request){
        $appSecret = preg_replace('/\s+/', '', trim($this->getSecret()));
        if(!$appSecret){
            throw new SecretNotSetupException();
        }
        $secretKey = $this->getHeaderKey();
        $headerToken = preg_replace('/\s+/', '', trim($request->header($secretKey)));
        if(!$request->hasHeader($secretKey)){
            throw new HeaderSecretKeyException('Secret Not Provided');
        }
        if($headerToken !== $appSecret){
            throw new ErrorClientTokenException('Error Client Secret');
        }
        $this->resolveManager($request);
    }

}