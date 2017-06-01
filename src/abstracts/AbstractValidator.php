<?php

namespace faiverson\gateways\abstracts;

use Illuminate\Validation\Factory;
use faiverson\gateways\contracts\Validable;

abstract class AbstractValidator implements Validable
{
    /**
     * Validator factory
     *
     * @var object
     */
    protected $validator;

    /**
     * Validator object
     *
     * @var object
     */
    protected $factory;

    /**
     * Data to be validated
     *
     * @var array
     */
    protected $data = array();

    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = array();

    /**
     * Messages
     *
     * @var array
     */
    protected $messages = array();

    /**
     * Validation errors
     *
     * @var array
     */
    protected $errors = array();

    public function __construct(Factory $validator)
    {
        $this->factory = $validator;
    }

    /**
     * Set data to validate
     *
     * @param array $data
     * @return self
     */
    public function with(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Return errors
     *
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * Pass the data and the rules to the validator
     *
     * @return boolean
     */
    public function passes()
    {
        $this->custom();
        $this->validator = $this->factory->make($this->data, $this->rules, $this->messages);
        $this->validator->after(function ($validator) {
            $this->after($validator);
        });

        if ($this->validator->fails()) {
            $this->errors = $this->validator->messages();
            return false;
        }

        return true;
    }

    public function custom()
    {
    }
    public function after($validator)
    {
    }
}
