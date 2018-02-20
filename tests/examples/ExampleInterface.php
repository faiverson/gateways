<?php

namespace App\Repositories\Interfaces;

use faiverson\gateways\contracts\RepositoryInterface;

/**
 * Note that we extend from RepositoryInterface, so any class that implements
 * this interface must also provide all the standard eloquent methods (find, all, etc.)
 */
interface ExampleInterface extends RepositoryInterface
{
}
