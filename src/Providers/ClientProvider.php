<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Client\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Myth\Api\Client\Commands\GetSecretCommand;
use Myth\Api\Client\Commands\MakeSecretCommand;
use Myth\Api\Client\Facades\Client;
use Myth\Api\Client\Http\Middleware\AuthenticateMiddleware;
use Myth\Api\Client\Utilities\MythApiClientWrapper;

/**
 * Class ClientProvider
 * @package Myth\Api\Client\Providers
 */
class ClientProvider extends ServiceProvider{

    /**
     * @var array
     */
    protected $commands = [
        MakeSecretCommand::class,
        GetSecretCommand::class,
    ];

    /**
     * Bootstrap the application services.
     * @return void
     */
    public function boot(){
        $this->publishes(
            [
                __DIR__."/../Config/mythclient.php" => config_path("mythclient.php"),

                __DIR__."/../Migrations/2020_03_28_171806_myth_api_client.php" => database_path(
                    "migrations/2020_03_28_171806_myth_api_client.php"
                ),
            ],
            "mythclient"
        );
    }

    /**
     * Register the application services.
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function register(){
        $this->mergeConfigFrom(__DIR__."/../Config/mythclient.php", "mythclient");
        $this->app->singleton(
            'myth.api.client',
            function(Application $app){
                return new MythApiClientWrapper($app, $app['config']['mythclient']);
            }
        );
        $this->app->make("Myth\\Api\Client\\Http\\Controllers\\ClientApiController");
        AliasLoader::getInstance()->alias("Myth\Api\Client", Client::class);
        $this->commands($this->commands);
        $router = $this->app['router'];
        $router->aliasMiddleware('myth.auth.client', AuthenticateMiddleware::class);

        Route::bind(
            'MythApiModelClient',
            function($value){
                return Client::resolveRouteBinding($value);
            }
        );
    }
}
