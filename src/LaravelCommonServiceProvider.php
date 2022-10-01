<?php

namespace Piod\LaravelCommon;

use Illuminate\Support\ServiceProvider;

class LaravelCommonServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {

        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
//        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'laravel_common');

        // Register the main class to use with the facade
//        $this->app->singleton('laravel_common', function () {
//            return new LaravelCommon;
//        });
        $this->commands([
            Console\healthCheck::class
        ]);

    }

    protected function registerPublishing()
    {
        $this->publishes([
            __DIR__.'/config/piod_common.php' => config_path('piod_common.php'),
        ], 'piod-common-config');
    }
}