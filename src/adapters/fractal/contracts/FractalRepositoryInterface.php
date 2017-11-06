<?php

namespace faiverson\gateways\adapters\fractal\contracts;

use faiverson\gateways\contracts\RepositoryInterface;

interface FractalRepositoryInterface extends RepositoryInterface
{
    public function transformer();

    public function response($resource, $data = [], $transformer = null);

    public function transformResponse($data, $resource);

    public function setIncludes($data);

    public function setTransformer($transformer, $data = null);

}