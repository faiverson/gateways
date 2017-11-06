<?php

namespace faiverson\gateways\adapters\fractal\abstracts;

use faiverson\gateways\abstracts\Gateway;
use faiverson\gateways\contracts\GatewayInterface;
use Illuminate\Foundation\Application;

/**
 * Class Gateway
 */
abstract class FractalGateway extends Gateway implements GatewayInterface
{
    /**
     * @var object fractal
     */
    protected $fractal;

    /**
     * @var array metadata
     */
    protected $meta;

    /**
     * @var string transformer
     */
    protected $transformer;

    public function __construct(Application $app, Fractable $fractal)
    {
        parent::__construct($app);
        $this->fractal = $fractal;
        $this->meta = $app['config']['repositories']['meta'];
    }

    /**
     * @param $resource
     * @param array $data
     *
     * We look into the dependency repository to get the proper response
     * using the transformer set by the repository
     * @return bool
     */
    public function response($resource, $data = [], $transformer = null)
    {
        $dependencies = $this->dependencies();
        foreach ($dependencies as $property => $interface) {
            $instance = $this->{$property}->model();
            if ($resource instanceof $instance) {
                if ($transformer) {
                    $this->{$property}->setTransformer($transformer, $data);
                }
                return $this->{$property}->transformResponse($this->{$property}->setAttributes($data), $resource);
            }

            // fallback if the response is a basic array and not a resource
            if (is_array($resource)) {
                $dependency = $this->{key($dependencies)};
                if ($transformer) {
                    $dependency->setTransformer($transformer, $data);
                }
                return $dependency->transformResponse($dependency->setAttributes($data), $resource);
            }
        }
        return false;
    }

}
