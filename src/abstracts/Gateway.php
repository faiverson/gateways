<?php

namespace faiverson\gateways\abstracts;

use faiverson\gateways\contracts\GatewayInterface;
use Illuminate\Foundation\Application;

/**
 * Class Gateway
 */
abstract class Gateway implements GatewayInterface
{
    public $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->setDependencies();
    }

    /**
     * Create new properties for each related model
     *
     */
    protected function setDependencies()
    {
        $dependencies = $this->dependencies();
        if (is_array($dependencies)) {
            foreach ($dependencies as $property => $interface) {
                $this->{$property} = $this->app->make($interface);
            }
        }
    }

    /**
     * Specify an array map of property name/class name
     *
     */
    abstract public function dependencies();
}
