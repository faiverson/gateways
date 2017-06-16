<?php

namespace faiverson\gateways\console;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

trait RepositoryCommandTrait
{
    protected $rawName;

    protected function getRawName()
    {
        return $this->rawName;
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $this->rawName = str_replace($this->type, '', $this->argument('name'));
        return trim(ucfirst($this->rawName));
    }

    /**
     * Determine if the class already exists.
     *
     * @param  string  $rawName
     * @return bool
     */
    protected function alreadyExists($rawName)
    {
        return class_exists($rawName);
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace_first($this->rootNamespace(), '', $name);
        return str_replace('\\', '/', $this->laravel['path']. DIRECTORY_SEPARATOR. $name). $this->type. '.php';
    }

    protected function getRepositoryNamespace()
    {
        return str_replace('/', '\\', $this->rootNamespace(). config('repositories.path.repositories'));
    }

    protected function getModelNamespace()
    {
        return str_replace('/', '\\', $this->rootNamespace().  config('repositories.path.models'));
    }

    protected function getTransformerNamespace()
    {
        return str_replace('/', '\\', $this->rootNamespace().  config('repositories.path.transformers'));
    }

    protected function getInterfaceNamespace()
    {
        return str_replace('/', '\\', $this->rootNamespace() . config('repositories.path.interfaces'));
    }

    protected function getInterface()
    {
        return ucfirst($this->getRawName()) . 'Interface';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $replace = [
            'DummyType' => $this->type,
            'DummyInterface' => $this->getInterface(),
            'DummyInstance' => '$' . strtolower($this->getRawName()),
            'RepoNamespaceModel' => $this->getModelNamespace(),
            'RepoNamespaceInterface' => $this->getInterfaceNamespace(),
            'RepoNamespaceTransformer' => $this->getTransformerNamespace(),
        ];
        return str_replace(array_keys($replace), array_values($replace), parent::buildClass($name));
    }
}
