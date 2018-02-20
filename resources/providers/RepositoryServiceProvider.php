<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * There is no need to bind classes into the container if they do not depend on any interfaces.
     * The container does not need to be instructed on how to build these objects,
     * since it can automatically resolve such "concrete" objects using PHP's reflection services.
     *
     */
    public function register()
    {
        // example line:
        //$this->app->bind(\App\Repositories\Interfaces\UserInterface::class, \App\Repositories\UserRepository::class);
    }

    public function provides()
    {
        return [
            \App\Repositories\Interfaces\UserInterface::class,
        ];
    }
}
