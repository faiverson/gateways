<?php

namespace faiverson\gateways\adapters\fractal;


use League\Fractal\TransformerAbstract;

/**
 * Class Transformer
 */
class Transformer extends TransformerAbstract
{
    protected $custom = [];

    public function __construct($data = null)
    {
        $this->custom = $data;
    }

    public function setCustomData($data)
    {
        $this->custom = $data;
    }

    public function getCustomData()
    {
        return $this->custom;
    }
}
