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

        $path = $this->laravel['path'].'/Http/Api/'.$apiVersion.'/Controllers/'.$domain.'/'.class_basename($name).'.php';

        return $path;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $domain = Str::studly($this->argument('domain'));
        $apiVersion = $this->option('api-version');

        return $rootNamespace.'\\Http\\Api\\'.$apiVersion.'\\Controllers\\'.$domain;
    }

    protected function buildClass($name)
    {
        // Get the stub content from parent
        $stub = parent::buildClass($name);
        $apiVersion = $this->option('api-version');

        // Define our target parent class
        $apiControllerClass = "App\\Http\\Api\\{$apiVersion}\\Controllers\\ApiController";
        $apiControllerImport = "use {$apiControllerClass};";

        // Add the ApiController import after namespace
        if (preg_match('/^(namespace\s+.+?;)(\s*)/m', $stub, $matches)) {
            $stub = str_replace(
                $matches[0],
                $matches[1].$matches[2]."\n".$apiControllerImport."\n",
                $stub
            );
        }

        // Handle the extends clause intelligently
        if (strpos($stub, 'extends Controller') !== false) {
            // Laravel found the default Controller and added extends clause
            $stub = str_replace('extends Controller', 'extends ApiController', $stub);
        } elseif (strpos($stub, 'extends ') === false) {
            // No extends clause found, add our own
            $stub = preg_replace(
                '/class\s+'.class_basename($name).'\s*\{/',
                'class '.class_basename($name).' extends ApiController'."\n{",
                $stub
            );
        }

        return $stub;
    }
}
