<?php

namespace faiverson\gateways\console;

use Illuminate\Foundation\Console\ModelMakeCommand;

class RepositoryModelMakeCommand extends ModelMakeCommand
{
    use RepositoryCommandTrait;

    protected $name = 'repository:model';

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\' . str_replace('/', '\\', config('repositories.path.models'));
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../stubs/model.stub';
    }

    protected function alreadyExists($rawName)
    {
        return $this->files->exists($this->getPath(config('repositories.path.models') . DIRECTORY_SEPARATOR . $rawName));
    }

    protected function getPath($name)
    {
        $name = str_replace_first($this->rootNamespace(), '', $name);

        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '.php';
    }
}
