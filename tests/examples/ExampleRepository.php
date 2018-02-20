<?php

namespace App\Repositories;

use App\Models\ExampleModel;
use App\Repositories\Interfaces\ExampleInterface;
use faiverson\gateways\abstracts\Repository as AbstractRepository;

class ExampleRepository extends AbstractRepository implements ExampleInterface
{
    public function model()
    {
        return ExampleModel::class;
    }
}