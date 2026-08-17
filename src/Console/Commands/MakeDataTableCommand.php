<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:datatable')]
class MakeDataTableCommand extends GeneratorCommand
{
    /**
     * @var string
     */
    protected $name = 'make:datatable';

    /**
     * @var string
     */
    protected $description = 'Create a new DataTableComponent class';

    /**
     * @var string
     */
    protected $type = 'DataTable';

    public function handle()
    {
        // Command::execute() casts this return value with (int) $result.
        // GeneratorCommand::handle() returns `false` when the class already
        // exists, which casts to 0 (int) false === 0 — a false "success".
        // Returning `true` instead casts to 1, a real failure exit code.
        $result = parent::handle();

        return $result === false ? true : $result;
    }

    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/datatable.stub');
    }

    protected function resolveStubPath(string $stub): string
    {
        $customPath = $this->laravel->basePath(trim($stub, '/'));

        return file_exists($customPath)
            ? $customPath
            : __DIR__.'/../../../resources'.$stub;
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Livewire';
    }

    protected function buildClass($name): string
    {
        $stub = parent::buildClass($name);

        return str_replace(
            array_keys($this->builderReplacements()),
            array_values($this->builderReplacements()),
            $stub
        );
    }

    /**
     * @return array<string, string>
     */
    protected function builderReplacements(): array
    {
        $model = $this->option('model');

        if (! is_string($model) || $model === '') {
            return [
                '{{ builderImport }}' => '',
                '{{ builderReturnType }}' => 'mixed',
                '{{ builderComment }}' => "// TODO: return an Eloquent Builder, a Query Builder, RawSql::query(...), an array, or a Collection.\n        ",
                '{{ builderBody }}' => '\App\Models\YourModel::query()',
            ];
        }

        $modelClass = Str::studly(class_basename(str_replace('/', '\\', $model)));
        $modelNamespace = Str::startsWith($model, '\\') ? ltrim($model, '\\') : 'App\\Models\\'.$modelClass;

        return [
            '{{ builderImport }}' => "use {$modelNamespace};\nuse Illuminate\Database\Eloquent\Builder;\n",
            '{{ builderReturnType }}' => 'Builder',
            '{{ builderComment }}' => '',
            '{{ builderBody }}' => "{$modelClass}::query()",
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'The Eloquent model to scaffold the builder() method for'],
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if it already exists'],
        ];
    }
}
