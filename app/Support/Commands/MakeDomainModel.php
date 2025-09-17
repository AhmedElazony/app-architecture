<?php

namespace App\Support\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Support\Str;

class MakeDomainModel extends ModelMakeCommand
{
    protected $signature = 'make:domain-model {domain} {name} {--all} {--controller} {--factory} {--force} {--migration} {--morph-pivot} {--pivot} {--policy} {--seed} {--resource} {--requests}';

    protected $description = 'Create a new Eloquent model for a specific domain';

    protected function getPath($name)
    {
        $domain = Str::studly($this->argument('domain'));
        
        $path = $this->laravel['path'] . '/Domains/' . $domain . '/Models/' . class_basename($name) . '.php';
        
        return $path;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $domain = Str::studly($this->argument('domain'));
        
        return $rootNamespace . '\\Domains\\' . $domain . '\\Models';
    }

    protected function createFactory()
    {
        if ($this->option('factory') || $this->option('all')) {
            $factory = Str::studly($this->argument('name'));
            
            $this->call('make:factory', [
                'name' => $factory . 'Factory',
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

            $this->call('make:api-controller', array_filter([
                'domain' => $domain,
                'name' => $controller . 'Controller',
                '--model' => $this->option('resource') || $this->option('all') ? $modelName : null,
                '--resource' => $this->option('resource') || $this->option('all'),
                '--requests' => $this->option('requests') || $this->option('all'),
            ]));
        }
    }
}