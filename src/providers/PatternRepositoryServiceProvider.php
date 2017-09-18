<?php

namespace faiverson\gateways\providers;

use faiverson\gateways\console\RepositoryControllerMakeCommand;
use faiverson\gateways\console\RepositoryInterfaceMakeCommand;
use faiverson\gateways\Console\RepositoryMakeCommand;
use faiverson\gateways\console\RepositoryModelMakeCommand;
use faiverson\gateways\console\TransformerMakeCommand;
use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;

class PatternRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../resources/config/repositories.php' => config_path('repositories.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../../resources/providers' => app_path('Providers'),
        ], 'gateway-provider');

        if ($this->app->runningInConsole()) {
            $this->commands([
                RepositoryMakeCommand::class,
                RepositoryInterfaceMakeCommand::class,
                RepositoryModelMakeCommand::class,
                RepositoryControllerMakeCommand::class,
            ]);
        }

        if (config('repositories.fractal')) {
            $this->commands([
                TransformerMakeCommand::class,
            ]);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        if(is_file(app_path('Providers') . '/RepositoryServiceProvider.php') && config('repositories.namespace')) {
            $this->app->register(            str_replace('/', '\\', config('repositories.namespace')) . '\RepositoryServiceProvider');
            if(config('repositories.fractal')) {
                $this->app->bind('League\Fractal\Serializer\SerializerAbstract', 'League\Fractal\Serializer\JsonApiSerializer');
                $this->app->bind('faiverson\gateways\adapters\fractal\abstracts\Fractable', 'faiverson\gateways\adapters\fractal\Fractal');
            }
        }
    }
}