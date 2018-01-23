<h1 align="center">Gateways</h1>
<p align="center">
<a href="https://travis-ci.org/faiverson/gateways"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/faiverson/gateway-pattern"><img src="https://poser.pugx.org/faiverson/gateway-pattern/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/faiverson/gateway-pattern"><img src="https://poser.pugx.org/faiverson/gateway-pattern/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/faiverson/gateway-pattern"><img src="https://poser.pugx.org/faiverson/gateway-pattern/license.svg" alt="License"></a>
</p>
[![Coverage Status](https://coveralls.io/repos/github/faiverson/gateways/badge.svg)](https://coveralls.io/github/faiverson/gateways)

## Gateways Installation

This package is a layer to interact between Controller and Model. If you want to create an abstraction layer to centralize all the queries in one class, instead of being adding models (eloquent queries) in a controller.
What about to repeat that code in a command or observer or any other peace of the puzzle. This is where Gateways are handy!
You only need to inject the Gateway dependency whereever you want.

- Install the package [Gateways](https://github.com/faiverson/gateways) using composer:<br> 
`composer require faiverson/gateway-pattern`
- Run the command: <br>
`php artisan vendor:publish`

Done!

## How to Use
- Go to the console and you can run: <br>
`
make:gateways:controller  Create a new controller class
make:gateways:full        Create a new Repository class
make:gateways:gateway     Create a new Gateway class
make:gateways:interface   Create a new Interface class
make:gateways:model       Create a new Eloquent model class
make:gateways:repository  Create a new Repository class
`
-Copy the line generated in your console into app/Providers/RepositoryServiceProvider.php
e.g. `$this->app->bind('App\Repositories\Interfaces\FooInterface', 'App\Repositories\FooRepository');
`

- create your route
## License

This package is open-sourced software licensed under the [Apache license](https://www.apache.org/licenses/LICENSE-2.0).
