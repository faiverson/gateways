<?php

namespace faiverson\gateways\console;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

class RepositoryInterfaceMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'repository:interface 
                            {name : the filename for create an interface for a repository}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Interface class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Interface';

    protected $path = [];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if (! $this->argument('name')) {
            return $this->error('Missing required argument');
        }
        elseif (Str::startsWith($this->getNameInput(), $this->laravel->getNamespace()) &&
            Str::startsWith($this->getNameInput(), 'Illuminate')) {
            return $this->error('You do not need to add the full path');
        }
        $this->path = config('repositories.path');
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
        return $rootNamespace. '\\' . str_replace('/', '\\', $this->path['interfaces']);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/interface.stub';
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

        return $this->laravel['path'].'/'.str_replace('\\', '/', $name). $this->type. '.php';
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
        ];
        return str_replace(array_keys($replace), array_values($replace), parent::buildClass($name));
    }
}
