<?php

namespace App\Support\Commands;

use Illuminate\Foundation\Console\PolicyMakeCommand;
use Illuminate\Support\Str;

class MakeDomainPolicy extends PolicyMakeCommand
{
    protected $signature = 'make:domain-policy {domain} {name} {--model=} {--guard=} {--force}';

    protected $description = 'Create a new policy for a specific domain';

    protected function getPath($name)
    {
        $domain = Str::studly($this->argument('domain'));

        $path = $this->laravel['path'].'/Domains/'.$domain.'/Policies/'.class_basename($name).'.php';

        return $path;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $domain = Str::studly($this->argument('domain'));

        return $rootNamespace.'\\Domains\\'.$domain.'\\Policies';
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        // Replace the default User model with our domain User model
        $stub = str_replace(
            'use Illuminate\Foundation\Auth\User',
            'use App\\Domains\\User\\Models\\User',
            $stub
        );

        // Also handle the case where it might use App\Models\User
        $stub = str_replace(
            'use App\\Models\\User',
            'use App\\Domains\\User\\Models\\User',
            $stub
        );

        return $stub;
    }
}
