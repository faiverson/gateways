<?php

namespace faiverson\gateways\providers;

use faiverson\gateways\adapters\fractal\serializer\ApiSerializer;
use faiverson\gateways\console\RepositoryControllerMakeCommand;
use faiverson\gateways\console\RepositoryInterfaceMakeCommand;
use faiverson\gateways\Console\RepositoryMakeCommand;
use faiverson\gateways\console\RepositoryModelMakeCommand;
use faiverson\gateways\console\TransformerMakeCommand;
use Illuminate\Support\ServiceProvider;

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

            if (config('repositories.fractal')) {
                $this->commands([
                    TransformerMakeCommand::class,
                ]);
            }
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        if (is_file(app_path('Providers') . '/RepositoryServiceProvider.php') && config('repositories.namespace')) {
            if (config('repositories.fractal')) {
                $this->app->singleton('League\Fractal\Serializer\SerializerAbstract', function ($app) {
                    return new ApiSerializer($app['config']['app']['url']);
                });
                $this->app->bind('faiverson\gateways\adapters\fractal\abstracts\Fractable',
                    'faiverson\gateways\adapters\fractal\Fractal');

                $this->app->bind('faiverson\gateways\contracts\GatewayInterface',
                    'faiverson\gateways\abstracts\Gateway');

                $this->app->bind('faiverson\gateways\contracts\GatewayInterface',
                    'faiverson\gateways\abstracts\Gateway');
            }
            $this->app->register(str_replace('/', '\\',
                    config('repositories.namespace')) . '\RepositoryServiceProvider');
        }
    }
}