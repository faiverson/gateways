<?php

namespace faiverson\gateways\console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class RepositoryMakeCommand extends GeneratorCommand
{
    use RepositoryCommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:gateways:repository 
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

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->argument('name')) {
            return $this->error('Missing required argument');
        }

        $name = $this->getNameInput();
        if (Str::startsWith($name, $this->rootNamespace()) &&
            Str::startsWith($name, 'Illuminate')) {
            return $this->error('You do not need to add the full path');
        }

        if(parent::handle()) {
            $replace = [
                'INTERFACE' => "\{$this->getInterfaceNamespace()}\\{$name}Interface::class",
                'REPOSITORY' => "\{$this->getRepositoryNamespace()}\\{$name}Repository::class",
            ];
            $bind_line = '$this->app->bind(INTERFACE, REPOSITORY);';
            $bind_line = str_replace(array_keys($replace), array_values($replace), $bind_line);
            $this->line("Don't forget to add this line to your RepositoryServiceProvider:");
            $this->info($bind_line);
        }
    }

    protected function alreadyExists($rawName)
    {
        return $this->files->exists($this->getPath(config('repositories.path.repositories') . DIRECTORY_SEPARATOR . $rawName));
    }

    protected function createMigration()
    {
        $table = Str::plural(Str::snake(class_basename($this->argument('name'))));
        $name = Str::studly("create_{$table}_table");

        if (!class_exists($name)) {
            $this->callSilent('make:migration', [
                'name' => strtolower(snake_case($name)),
                '--create' => $table,
            ]);
        }
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return str_replace('/', '\\', $rootNamespace . DIRECTORY_SEPARATOR . config('repositories.path.repositories'));
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../stubs/repository.stub';
    }
}
