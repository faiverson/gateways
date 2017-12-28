<?php

namespace faiverson\gateways\console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class GatewayMakeCommand extends GeneratorCommand
{
    use RepositoryCommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:gateways:gateway 
                            {name : the filename for the repository}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Gateway class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Gateway';

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
        parent::handle();
    }

    protected function alreadyExists($rawName)
    {
        return $this->files->exists($this->getPath(config('repositories.path.gateways') . DIRECTORY_SEPARATOR . $rawName));
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return str_replace('/', '\\', $rootNamespace . DIRECTORY_SEPARATOR . config('repositories.path.gateways'));
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../stubs/gateway.stub';
    }
}
