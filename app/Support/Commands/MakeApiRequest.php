<?php

namespace App\Support\Commands;

use Illuminate\Foundation\Console\RequestMakeCommand;
use Illuminate\Support\Str;

class MakeApiRequest extends RequestMakeCommand
{
    protected $signature = 'make:api-request {domain} {name} {--api-version=V1} {--force}';

    protected $description = 'Create a new form request class for API domain';

    protected function getPath($name)
    {
        $domain = Str::studly($this->argument('domain'));
        $apiVersion = $this->option('api-version');

        $path = $this->laravel['path'].'/Http/Api/'.$apiVersion.'/Requests/'.$domain.'/'.class_basename($name).'.php';

        return $path;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $domain = Str::studly($this->argument('domain'));
        $apiVersion = $this->option('api-version');

        return $rootNamespace.'\\Http\\Api\\'.$apiVersion.'\\Requests\\'.$domain;
    }
}
