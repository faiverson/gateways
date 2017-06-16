<?php

namespace faiverson\gateways\console;

use Illuminate\Console\GeneratorCommand;

class TransformerMakeCommand extends GeneratorCommand
{
    use RepositoryCommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'repository:transformer 
                            {name : the filename for create a transformer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Transformer to interact with Fractal';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Transformer';

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace. '\\' . str_replace('/', '\\', config('repositories.path.transformers'));
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../adapters/fractal/stubs/transformer.stub';
    }
}
