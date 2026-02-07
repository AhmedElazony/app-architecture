<?php

namespace App\Support\Commands;

use Illuminate\Foundation\Console\ResourceMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeApiResource extends ResourceMakeCommand
{
    protected $signature = 'make:api-resource {domain} {name} {--api-version=V1} {--collection} {--json-api} {--force}';

    protected $description = 'Create a new Eloquent resource class for API domain';

    protected function getPath($name)
    {
        $domain = Str::studly($this->argument('domain'));
        $apiVersion = Str::upper($this->option('api-version'));

        $path = $this->laravel['path'].'/Http/Api/'.$apiVersion.'/Resources/'.$domain.'/'.class_basename($name).'.php';

        return $path;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $domain = Str::studly($this->argument('domain'));
        $apiVersion = Str::upper($this->option('api-version'));

        return $rootNamespace.'\\Http\\Api\\'.$apiVersion.'\\Resources\\'.$domain;
    }

    protected function getArguments()
    {
        return [
            ['domain', InputArgument::REQUIRED, 'The name of the domain'],
            ['name', InputArgument::REQUIRED, 'The name of the class'],
        ];
    }

    protected function getOptions()
    {
        return [
            ['api-version', null, InputOption::VALUE_OPTIONAL, 'The API version for the resource (e.g., V1)', 'V1'],
            ['collection', 'c', InputOption::VALUE_NONE, 'Indicates that the resource is a collection'],
            ['json-api', null, InputOption::VALUE_NONE, 'Indicates that the resource should be generated in JSON API format'],
            ['force', 'f', InputOption::VALUE_NONE, 'Overwrite existing files'],
        ];
    }
}
