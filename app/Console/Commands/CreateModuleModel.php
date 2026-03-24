<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class CreateModuleModel extends Command
{
    // Usage: php artisan module:model Billing Invoice
    protected $signature = 'module:model {module} {model} {--m|migration}';
    protected $description = 'Create a new model and migration within a specific module';

    public function handle(): void
    {
        $module = Str::studly($this->argument('module'));
        $modelName = Str::studly($this->argument('model'));
        
        $modelPath = "App\\Modules\\{$module}\\Models\\{$modelName}";
        
        // 1. Create the Model
        Artisan::call('make:model', [
            'name' => $modelPath
        ]);
        $this->info("Model created: {$modelPath}");

        // 2. Handle Migration if requested
        if ($this->option('migration')) {
            $tableName = Str::snake(Str::pluralStudly($modelName));
            
            // Create the migration in the default folder first
            Artisan::call('make:migration', [
                'name' => "create_{$tableName}_table",
                '--create' => $tableName
            ]);

            // Define paths
            $moduleMigrationPath = "app/Modules/{$module}/Database/Migrations";
            File::ensureDirectoryExists(base_path($moduleMigrationPath));

            // Move the latest migration file
            $migrations = File::files(database_path('migrations'));
            $latestMigration = end($migrations);
            $fileName = $latestMigration->getFilename();

            File::move(
                $latestMigration->getRealPath(),
                base_path("{$moduleMigrationPath}/{$fileName}")
            );

            $this->info("Migration moved to: {$moduleMigrationPath}/{$fileName}");
        }
    }
}