<?php

namespace faiverson\gateways\tests;

use App\Models\ExampleModel;
use App\Repositories\ExampleRepository;

class RepositoryTest extends TestCase
{
    public function setUp()
    {
        require __DIR__ . '/../vendor/autoload.php';
    }

    public function testRepository()
    {
//        $example = \Mockery::mock(ExampleModel::class);
//        dd($example->toArray());
    }
}