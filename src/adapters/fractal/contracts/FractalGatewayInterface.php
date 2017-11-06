<?php

namespace faiverson\gateways\adapters\fractal\contracts;

use faiverson\gateways\contracts\GatewayInterface;

interface FractalGatewayInterface extends GatewayInterface
{
    public function response($resource, $data = [], $transformer = null);

    public function dependencies();
}
