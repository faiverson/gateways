<h1 align="center">Gateways</h1>
<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/faiverson/gateway-pattern"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/faiverson/gateway-pattern"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/faiverson/gateway-pattern"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## Gateways Installation

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as:

- Install the package [Gateways](https://github.com/faiverson/gateways).
- Run the command: <br>
`php artisan vendor:publish`

Done!

## How to Use
- Go to the console and run for example: <br>
`php artisan make:repository Foo`
-Copy the line generated in your console into app/Providers/RepositoryServiceProvider.php
e.g. `$this->app->bind('App\Repositories\Interfaces\Foonterface', 'App\Repositories\FooRepository');
`

- create your route
## License

This package is open-sourced software licensed under the [Apache license](https://www.apache.org/licenses/LICENSE-2.0).
