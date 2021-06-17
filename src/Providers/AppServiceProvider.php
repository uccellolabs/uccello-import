<?php

namespace Uccello\Import\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Uccello\Import\Http\Livewire\ConfigMapping;
use Uccello\Import\Http\Livewire\ImportManager;
use Uccello\Import\Http\Livewire\UploadFile;

/**
 * App Service Provider
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
        // Views
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'import');

        // Translations
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'import');

        // Migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // Routes
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');

        // Publish assets
        $this->publishes([
            __DIR__ . '/../../public' => public_path('vendor/uccello/import'),
        ], 'import-assets');

        Livewire::component('import-manager', ImportManager::class);
        Livewire::component('import-upload-file', UploadFile::class);
        Livewire::component('import-config-mapping', ConfigMapping::class);
    }

    public function register()
    {
        //
    }
}
