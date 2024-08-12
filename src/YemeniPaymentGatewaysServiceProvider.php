<?php

namespace Aymanalhattami\YemeniPaymentGateways;

use Illuminate\Support\ServiceProvider;

class YemeniPaymentGatewaysServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'yemeni-payment-gateways');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'yemeni-payment-gateways');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/yemeni-payment-gateways.php' => config_path('yemeni-payment-gateways.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/yemeni-payment-gateways'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/yemeni-payment-gateways'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/yemeni-payment-gateways'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/yemeni-payment-gateways.php', 'yemeni-payment-gateways');

        // Register the main class to use with the facade
        $this->app->singleton('yemeni-payment-gateways', function () {
            return new YemeniPaymentGateways;
        });
    }
}
