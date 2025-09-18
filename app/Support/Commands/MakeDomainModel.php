<?php

namespace App\Support\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Support\Str;

class MakeDomainModel extends ModelMakeCommand
{
    protected $signature = 'make:domain-model {domain} {name} {--all} {--controller} {--factory} {--force} {--migration} {--morph-pivot} {--pivot} {--policy} {--seed} {--resource} {--requests}';

    protected $description = 'Create a new Eloquent model for a specific domain';

    public function handle()
    {
        try {
            // Call parent handle method
            $result = parent::handle();

            // Create additional components only if model creation was successful
            $this->createFactory();
            $this->createMigration();
            $this->createController();
            $this->createDomainPolicy();

            return $result;
        } catch (\InvalidArgumentException $e) {
            // Suppress the "api option does not exist" error since it doesn't affect functionality
            if (str_contains($e->getMessage(), 'The "api" option does not exist')) {
                // Model was created successfully, just continue with additional components
                $this->createFactory();
                $this->createMigration();
                $this->createController();
                $this->createDomainPolicy();

                return;
            }

            // Re-throw other exceptions
            throw $e;
        }
    }

    // Override parent method that causes the "api" option error
    protected function createModelController()
    {
        // Disable parent's automatic controller creation

    }

    // Override parent's policy creation to prevent default location
    protected function createPolicy()
    {
        // Disable parent's automatic policy creation - we handle it in createDomainPolicy

    }

    protected function getPath($name)
    {
        $domain = Str::studly($this->argument('domain'));

        $path = $this->laravel['path'].'/Domains/'.$domain.'/Models/'.class_basename($name).'.php';

        return $path;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $domain = Str::studly($this->argument('domain'));

        return $rootNamespace.'\\Domains\\'.$domain.'\\Models';
    }

    protected function createFactory()
    {
        if ($this->option('factory') || $this->option('all')) {
            $factory = Str::studly($this->argument('name'));

            $this->call('make:factory', [
                'name' => $factory.'Factory',
                '--model' => $this->qualifyClass($this->getNameInput()),
            ]);
        }
    }

    protected function createMigration()
    {
        if ($this->option('migration') || $this->option('all')) {
            $table = Str::snake(Str::pluralStudly(class_basename($this->argument('name'))));

            if ($this->option('pivot')) {
                $table = Str::singular($table);
            }

            $this->call('make:migration', [
                'name' => "create_{$table}_table",
                '--create' => $table,
            ]);
        }
    }

    protected function createController()
    {
        if ($this->option('controller') || $this->option('resource') || $this->option('requests') || $this->option('all')) {
            $controller = Str::studly(class_basename($this->argument('name')));
            $domain = $this->argument('domain');
            $modelName = $this->qualifyClass($this->getNameInput());

            // Build arguments array carefully
            $arguments = [
                'domain' => $domain,
                'name' => $controller.'Controller',
            ];

            // Add options only if they should be included
            if ($this->option('resource') || $this->option('all')) {
                $arguments['--resource'] = true;
            }

            if ($this->option('requests') || $this->option('all')) {
                $arguments['--requests'] = true;
            }

            if (($this->option('resource') || $this->option('all')) && $modelName) {
                $arguments['--model'] = $modelName;
            }

            $this->call('make:api-controller', $arguments);
        }
    }

    protected function createDomainPolicy()
    {
        if ($this->option('policy') || $this->option('all')) {
            $policy = Str::studly(class_basename($this->argument('name')));
            $domain = $this->argument('domain');
            $modelName = $this->qualifyClass($this->getNameInput());

            $arguments = [
                'domain' => $domain,
                'name' => $policy.'Policy',
            ];

            // Add model option if we have a model
            if ($modelName) {
                $arguments['--model'] = $modelName;
            }

            $this->call('make:domain-policy', $arguments);
        }
    }
}
