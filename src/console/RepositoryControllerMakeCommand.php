<?php

namespace faiverson\gateways\console;

use Illuminate\Routing\Console\ControllerMakeCommand;

class RepositoryControllerMakeCommand extends ControllerMakeCommand
{
    protected $name = 'repository:controller';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->rawName = str_replace('Controller', '', $this->argument('name'));
        parent::fire();
    }

    protected function getNameInput()
    {
        return trim(ucfirst($this->rawName)) . 'Controller';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/controller.stub';
    }

    protected function getInterfaceNamespace()
    {
        return $this->rootNamespace(). str_replace('/', '\\', config('repositories.path.interfaces'));
    }

    protected function getInterface()
    {
        return ucfirst($this->rawName) . 'Interface';
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
            'DummyInterfaceNamespace' => $this->getInterfaceNamespace(),
            'DummyName' => strtolower($this->rawName),
            'DummyInterface' => $this->getInterface(),
        ];
        return str_replace(array_keys($replace), array_values($replace), parent::buildClass($name));
    }
}
