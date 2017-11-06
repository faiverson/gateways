<?php

namespace faiverson\gateways\console;

use Illuminate\Routing\Console\ControllerMakeCommand;

class RepositoryControllerMakeCommand extends ControllerMakeCommand
{
    use RepositoryCommandTrait;

    protected $name = 'repository:controller';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../stubs/controller.stub';
    }

    protected function alreadyExists($rawName)
    {
        return $this->files->exists($this->getPath(config('repositories.path.controllers') . DIRECTORY_SEPARATOR . $rawName));
    }
}
