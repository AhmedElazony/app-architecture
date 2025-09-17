<?php

namespace App\Support\Commands;

use Illuminate\Routing\Console\ControllerMakeCommand;
use Illuminate\Support\Str;

class MakeApiController extends ControllerMakeCommand
{
    protected $signature = 'make:api-controller {domain} {name} {--api-version=V1} {--api} {--type=} {--force} {--invokable} {--model=} {--parent=} {--resource} {--requests} {--singleton} {--creatable} {--test} {--pest}';

    protected $description = 'Create a new API controller for a specific domain';

    protected function getPath($name)
    {
        $domain = Str::studly($this->argument('domain'));
        $apiVersion = $this->option('api-version');
        
        $path = $this->laravel['path'] . '/Http/Api/' . $apiVersion . '/Controllers/' . $domain . '/' . class_basename($name) . '.php';
        
        return $path;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $domain = Str::studly($this->argument('domain'));
        $apiVersion = $this->option('api-version');
        
        return $rootNamespace . '\\Http\\Api\\' . $apiVersion . '\\Controllers\\' . $domain;
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);
        
        // Replace the parent class to use ApiController
        $apiVersion = $this->option('api-version');
        $stub = str_replace(
            'use Illuminate\Http\Request;',
            "use Illuminate\Http\Request;\nuse App\\Http\\Api\\{$apiVersion}\\Controllers\\ApiController;",
            $stub
        );
        
        $stub = str_replace(
            'extends Controller',
            'extends ApiController',
            $stub
        );
        
        return $stub;
    }
}