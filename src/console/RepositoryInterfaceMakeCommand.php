<?php

namespace faiverson\gateways\console;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

class RepositoryInterfaceMakeCommand extends GeneratorCommand
{

    use RepositoryCommandTrait;

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

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace. '\\' . str_replace('/', '\\', config('repositories.path.interfaces'));
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if(config('repositories.fractal')) {
            return __DIR__.'/../adapters/fractal/stubs/interface.stub';
        }
        return __DIR__.'/../stubs/interface.stub';
    }

    protected function getPath($name)
    {
        $name = str_replace_first($this->rootNamespace(), '', $name);

        return $this->laravel['path'].'/'.str_replace('\\', '/', $name).'.php';
    }

    protected function alreadyExists($rawName)
    {
        return $this->files->exists($this->getPath(config('repositories.path.interfaces') . DIRECTORY_SEPARATOR . $rawName));
    }
}
