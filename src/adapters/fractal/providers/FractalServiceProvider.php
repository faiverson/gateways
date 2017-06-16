<?php

namespace faiverson\gateways\adapters\fractal\providers;

use Illuminate\Support\ServiceProvider;

class FractalServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('League\Fractal\Serializer\SerializerAbstract', 'League\Fractal\Serializer\JsonApiSerializer');
        $this->app->bind('faiverson\gateways\adapters\fractal\abstracts\Fractable', 'faiverson\gateways\adapters\fractal\Fractal');
    }

}