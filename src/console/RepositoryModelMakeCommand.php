<?php

namespace faiverson\gateways\console;

use Illuminate\Foundation\Console\ModelMakeCommand;

class RepositoryModelMakeCommand extends ModelMakeCommand
{
    protected $name = 'repository:model';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        parent::fire();
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return trim(ucfirst($this->argument('name')));
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace. '\\' . str_replace('/', '\\', config('repositories.path.models'));
    }
}
