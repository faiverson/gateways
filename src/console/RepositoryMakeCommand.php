<?php

namespace faiverson\gateways\console;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

class RepositoryMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:repository 
                            {name : the filename for the repository}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Repository class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

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
        $name = $this->getNameInput();
        if (Str::startsWith($name, $this->rootNamespace()) &&
            Str::startsWith($name, 'Illuminate')) {
            return $this->error('You do not need to add the full path');
        }

        $this->callSilent('repository:interface', ['name' => $name]);
        $this->callSilent('repository:model', ['name' => $name]);
        $this->callSilent('repository:controller', ['name' => $name]);
        $this->createMigration();
        parent::fire();

        $replace = [
            'INTERFACE' => "'{$this->getInterfaceNamespace()}\\{$name}Interface'",
            'REPOSITORY' => "'{$this->getRepositoryNamespace()}\\{$name}Repository'",
        ];
        $bind_line = '$this->app->bind(INTERFACE, REPOSITORY);';
        $bind_line = str_replace(array_keys($replace), array_values($replace), $bind_line);
        $this->line("Don't forget to add this line to your RepositoryServiceProvider:");
        $this->info($bind_line);
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
        return str_replace('/', '\\', $rootNamespace. DIRECTORY_SEPARATOR. config('repositories.path.repositories'));
    }

    protected function getRepositoryNamespace()
    {
        return str_replace('/', '\\', $this->rootNamespace(). config('repositories.path.repositories'));
    }

    protected function getModelNamespace()
    {
        return str_replace('/', '\\', $this->rootNamespace().  config('repositories.path.models'));
    }

    protected function getInterfaceNamespace()
    {
        return str_replace('/', '\\', $this->rootNamespace() . config('repositories.path.interfaces'));
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/repository.stub';
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

    protected function createMigration()
    {
        $table = Str::plural(Str::snake(class_basename($this->argument('name'))));

        $this->callSilent('make:migration', [
            'name' => "create_{$table}_table",
            '--create' => $table,
        ]);
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
            'DummyType' => $this->type,
            'DummyModelNamespace' => $this->getModelNamespace(),
            'DummyInterface' => $this->rootNamespace() . $name,
        ];

        return str_replace(array_keys($replace), array_values($replace), parent::buildClass($name));
    }
}
