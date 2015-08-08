<?php

namespace PulkitJalan\Google;

use Illuminate\Support\ServiceProvider;

class GoogleServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        $this->app['PulkitJalan\Google\Client'] = function ($app) {
            return $app['google.api.client'];
        };

        $this->publishes([
            __DIR__.'/config/config.php' => config_path('google.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'google');

        $this->app['google.api.client'] = $this->app->share(function () {
            return new Client(config('google'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return ['google.api.client', 'PulkitJalan\Google\Client'];
    }
}
