<?php

namespace App\Support\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeDomainService extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'make:domain-service {domain} {service} {--bind}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new domain service with interface';

    /**
     * The type of class being generated.
     */
    protected $type = 'Service';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $domain = $this->argument('domain');
        $service = $this->argument('service');

        // Create the qualified class name
        $name = $this->qualifyClass($service);
        $path = $this->getPath($name);

        // First, create the interface
        $this->createInterface();

        // Then create the service implementation
        if ($this->alreadyExists($service)) {
            $this->components->error($this->type.' already exists.');

            return false;
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->sortImports($this->buildClass($name)));

        $this->components->info(sprintf('%s [%s] created successfully.', $this->type, $path));

        // Optionally register the service binding
        if ($this->option('bind')) {
            $this->registerServiceBinding();
        }

        return true;
    }

    /**
     * Create the service interface
     */
    protected function createInterface()
    {
        $domain = $this->argument('domain');
        $service = $this->argument('service');

        $interfaceName = $service.'Interface';
        $interfacePath = app_path("Domains/{$domain}/Services/Contracts/{$interfaceName}.php");

        // Create directory if it doesn't exist
        $this->makeDirectory($interfacePath);

        $interfaceContent = $this->buildInterfaceClass($domain, $service);

        if (! file_exists($interfacePath)) {
            file_put_contents($interfacePath, $interfaceContent);
            $this->components->info("Interface [{$interfacePath}] created successfully.");
        }
    }

    /**
     * Build the interface class content
     */
    protected function buildInterfaceClass($domain, $service)
    {
        $interfaceName = $service.'Interface';

        return "<?php

namespace App\\Domains\\{$domain}\\Services\\Contracts;

interface {$interfaceName}
{
    //
}
";
    }

    /**
     * Get the stub file for the generator.
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/domain-service.stub';
    }

    /**
     * Get the default namespace for the class.
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        $domain = $this->argument('domain');

        return $rootNamespace."\\Domains\\{$domain}\\Services\\Database";
    }

    /**
     * Build the class with the given name.
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)
            ->replaceDomain($stub)
            ->replaceService($stub)
            ->replaceClass($stub, $name);
    }

    /**
     * Replace the domain placeholder
     */
    protected function replaceDomain(&$stub)
    {
        $domain = $this->argument('domain');
        $stub = str_replace('{{ domain }}', $domain, $stub);

        return $this;
    }

    /**
     * Replace the service placeholder
     */
    protected function replaceService(&$stub)
    {
        $service = $this->argument('service');
        $stub = str_replace('{{ service }}', $service, $stub);

        return $this;
    }

    /**
     * Get the desired class name from the input.
     */
    protected function getNameInput()
    {
        return $this->argument('service');
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments()
    {
        return [
            ['domain', InputArgument::REQUIRED, 'The domain name'],
            ['service', InputArgument::REQUIRED, 'The service name'],
        ];
    }

    /**
     * Register the service binding in DomainServiceProvider
     */
    protected function registerServiceBinding()
    {
        $domain = $this->argument('domain');
        $service = $this->argument('service');

        $interfaceClass = "App\\Domains\\{$domain}\\Services\\Contracts\\{$service}Interface";
        $serviceClass = "App\\Domains\\{$domain}\\Services\\Database\\{$service}";

        $providerPath = app_path('Support/Providers/DomainServiceProvider.php');

        if (file_exists($providerPath)) {
            $content = file_get_contents($providerPath);

            // Check if binding already exists
            $bindingCheck = "{$service}Interface::class => {$service}::class";
            if (strpos($content, $bindingCheck) !== false) {
                $this->components->warn('Service binding already exists in DomainServiceProvider.');

                return;
            }

            // Add use statements
            $this->addUseStatements($content, $interfaceClass, $serviceClass);

            // Add binding to services array
            $this->addServiceBinding($content, $service);

            file_put_contents($providerPath, $content);
            $this->components->info('Service binding registered in DomainServiceProvider.');

            // Format the DomainServiceProvider with Laravel Pint
            $this->formatWithPint($providerPath);
        } else {
            $this->components->error('DomainServiceProvider not found.');
        }
    }

    /**
     * Format file with Laravel Pint
     */
    protected function formatWithPint($filePath)
    {
        $pintPath = base_path('vendor/bin/pint');

        if (file_exists($pintPath)) {
            exec("$pintPath $filePath 2>/dev/null", $output, $returnCode);

            if ($returnCode === 0) {
                $this->components->info('DomainServiceProvider formatted with Laravel Pint.');
            }
        }
    }

    /**
     * Add service binding to the services array
     */
    protected function addServiceBinding(&$content, $service)
    {
        $binding = "            {$service}Interface::class => {$service}::class,";

        // Check if array is empty
        if (preg_match('/\$services = \[\s*\n\s*\]/', $content)) {
            // Replace empty array
            $content = preg_replace(
                '/\$services = \[\s*\n\s*\]/',
                '$services = ['."\n".$binding."\n        ".']',
                $content
            );
        } else {
            // Add to existing array - find the last binding and add after it
            $content = preg_replace(
                '/(\$services = \[.*?)(\n\s*\];)/s',
                '$1'."\n".$binding.'$2',
                $content
            );
        }
    }

    /**
     * Add use statements to the provider file
     */
    protected function addUseStatements(&$content, $interfaceClass, $serviceClass)
    {
        // Clean the class names (remove leading backslashes)
        $interfaceClass = ltrim($interfaceClass, '\\');
        $serviceClass = ltrim($serviceClass, '\\');

        $interfaceUse = "use {$interfaceClass};";
        $serviceUse = "use {$serviceClass};";

        // Add interface use statement if it doesn't exist
        if (strpos($content, $interfaceUse) === false) {
            // Add after ServiceProvider use statement
            $content = preg_replace(
                '/(use Illuminate\\\\Support\\\\ServiceProvider;\s*\n)/',
                '$1'.$interfaceUse."\n",
                $content
            );
        }

        // Add service use statement if it doesn't exist
        if (strpos($content, $serviceUse) === false) {
            // Add after the interface use statement
            $content = preg_replace(
                '/('.preg_quote($interfaceUse, '/').'\s*\n)/',
                '$1'.$serviceUse."\n",
                $content
            );
        }

        // Ensure there's an empty line before the class declaration
        $content = preg_replace(
            '/(use [^;]+;\s*\n)(class)/',
            '$1'."\n".'$2',
            $content
        );

    }

    /**
     * Get the console command options.
     */
    protected function getOptions()
    {
        return [
            ['bind', 'b', InputOption::VALUE_NONE, 'Register the service binding in AppServiceProvider'],
        ];
    }
}
