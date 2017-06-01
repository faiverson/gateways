<?php

namespace faiverson\gateways\providers;

use faiverson\gateways\console\RepositoryControllerMakeCommand;
use faiverson\gateways\console\RepositoryInterfaceMakeCommand;
use faiverson\gateways\Console\RepositoryMakeCommand;
use faiverson\gateways\console\RepositoryModelMakeCommand;
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
        ], 'provider');

        if ($this->app->runningInConsole()) {
            $this->commands([
                RepositoryMakeCommand::class,
                RepositoryInterfaceMakeCommand::class,
                RepositoryModelMakeCommand::class,
                RepositoryControllerMakeCommand::class,
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
        $this->app->register('App\Providers\RepositoryServiceProvider');
    }
}