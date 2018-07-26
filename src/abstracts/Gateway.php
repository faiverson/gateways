<?php

namespace faiverson\gateways\abstracts;

use Illuminate\Foundation\Application;

/**
 * Class Gateway
 */
abstract class Gateway extends Repository
{
    /**
     * Specify an array map of property name/class name
     *
     */
    abstract public function dependencies();

    public function __construct(Application $app)
    {
        parent::__construct($app);
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


}
