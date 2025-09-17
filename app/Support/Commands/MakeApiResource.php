<?php

namespace App\Support\Commands;

use Illuminate\Foundation\Console\ResourceMakeCommand;
use Illuminate\Support\Str;

class MakeApiResource extends ResourceMakeCommand
{
    protected $signature = 'make:api-resource {domain} {name} {--api-version=V1} {--collection} {--force}';

    protected $description = 'Create a new Eloquent resource class for API domain';

    protected function getPath($name)
    {
        $domain = Str::studly($this->argument('domain'));
        $apiVersion = $this->option('api-version');
        
        $path = $this->laravel['path'] . '/Http/Api/' . $apiVersion . '/Resources/' . $domain . '/' . class_basename($name) . '.php';
        
        return $path;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $domain = Str::studly($this->argument('domain'));
        $apiVersion = $this->option('api-version');
        
        return $rootNamespace . '\\Http\\Api\\' . $apiVersion . '\\Resources\\' . $domain;
    }
}