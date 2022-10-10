<?php

namespace Piod\LaravelCommon;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Piod\LaravelCommon\Http\Middleware\PioAuthenticate;


class LaravelCommonServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {

        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('pioAuth', PioAuthenticate::class);

        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->commands([
            Console\healthCheck::class,
            Console\IncomingDataFromRabbit::class,
            Console\IncomingEventFromRabbit::class,
        ]);

        Response::macro('custom', function ($data = null, $message = null, int $httpCode = 200, $code = null) {
            return response()->json([
                "data" => $data ?? null,
                "message" => $message ?? "",
                'code' => $code ?? 0,
            ], $httpCode);
        });

    }

    protected function registerPublishing()
    {
        $this->publishes([
            __DIR__.'/app' => app_path('/'),
        ], 'piod:app');

        $this->publishes([
            __DIR__.'/config/piod_common.php' => config_path('piod_common.php'),
        ], 'piod:config:common');

        $this->publishes([
            __DIR__.'/config/logging.php' => config_path('logging.php'),
        ], 'piod:config:logging');


    }
}