<?php

namespace Batiscaff\FieldsKit;

use Batiscaff\FieldsKit\Console\Commands\MultilingualConvert;
use Batiscaff\FieldsKit\Contracts\PeculiarField;
use Batiscaff\FieldsKit\Contracts\PeculiarFieldData;
use Batiscaff\FieldsKit\Http\Livewire\LivewirePeculiarFieldAddButton;
use Batiscaff\FieldsKit\Http\Livewire\LivewirePeculiarFieldEdit;
use Batiscaff\FieldsKit\Http\Livewire\LivewirePeculiarFieldLanguageFlag;
use Batiscaff\FieldsKit\Http\Livewire\LivewirePeculiarFieldLanguageSwitcher;
use Batiscaff\FieldsKit\Http\Livewire\LivewirePeculiarFields;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

/**
 * Class FieldsKitServiceProvider.
 * @package Batiscaff\FieldsKit
 */
class FieldsKitServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/fields-kit.php',
            'fields-kit'
        );
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->bindClasses();
        $this->configureComponents();
        $this->configureRoutes();
        $this->configureLanguages();
        $this->configurePermissions();
        $this->configureCommands();

        $this->loadViewsFrom(__DIR__ . '/../resources/views/livewire', 'fields-kit');

        if ($this->app->runningInConsole()) {
            $this->offerPublishing();
        }
    }

    /**
     * @return void
     */
    protected function offerPublishing(): void
    {
        if (! function_exists('config_path')) {
            // function not available and 'publish' not relevant in Lumen
            return;
        }

        $this->publishes([
            __DIR__.'/../config/fields-kit.php' => config_path('fields-kit.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/create_peculiar_fields_table.php.stub' => $this->getMigrationFileName('create_peculiar_fields_table.php'),
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/../resources/views/livewire' => $this->app->resourcePath('views/vendor/fields-kit'),
        ], 'views');

        $this->publishes([
            __DIR__ . '/../resources/lang' => $this->app->resourcePath('lang/vendor/fields-kit'),
        ], 'translation');

    }

    /**
     * @return void
     */
    protected function bindClasses(): void
    {
        $this->app->bind(PeculiarField::class, config('fields-kit.models.peculiar_fields'));
        $this->app->bind(PeculiarFieldData::class, config('fields-kit.models.peculiar_fields_data'));
    }

    /**
     * @return void
     */
    protected function configureComponents(): void
    {
        Livewire::component('peculiar-fields', LivewirePeculiarFields::class);
        Livewire::component('peculiar-field-edit', LivewirePeculiarFieldEdit::class);
        Livewire::component('peculiar-field-add-button', LivewirePeculiarFieldAddButton::class);
        Livewire::component('peculiar-field-language-flag', LivewirePeculiarFieldLanguageFlag::class);
        Livewire::component('peculiar-field-language-switcher', LivewirePeculiarFieldLanguageSwitcher::class);

        foreach (config('fields-kit.types') as $key => $class) {
            if (class_exists($class) && class_exists($class::livewireClass())) {
                Livewire::component('fields-kit-' . $key, $class::livewireClass());
            }
        }
    }

    /**
     * @return void
     */
    protected function configureRoutes(): void
    {
        Route::group([
            'prefix'     => config('fields-kit.prefix', ''),
            'middleware' => config('fields-kit.middleware', ['web'])
        ], function () {
            Route::get('/p-field/{currentField}', LivewirePeculiarFieldEdit::class)
                ->name('fields-kit.peculiar-field-edit')
            ;
        });

    }

    /**
     * @return void
     */
    protected function configureLanguages(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'fields-kit');
    }

    /**
     * @return void
     */
    protected function configurePermissions(): void
    {
        foreach (config('fields-kit.permission.peculiar-field') as $permission) {
            app(Gate::class)->define($permission, function ($user) use ($permission) {
                if (method_exists($user, 'checkPermissionTo')) {
                    return $user->checkPermissionTo($permission);
                }

                return true;
            });
        }
    }

    /**
     * @return void
     */
    protected function configureCommands(): void
    {
        $this->commands([
            MultilingualConvert::class,
        ]);
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @return string
     */
    protected function getMigrationFileName($migrationFileName): string
    {
        $timestamp = date('Y_m_d_His');

        $filesystem = $this->app->make(Filesystem::class);

        return Collection::make($this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem, $migrationFileName) {
                return $filesystem->glob($path.'*_'.$migrationFileName);
            })
            ->push($this->app->databasePath()."/migrations/{$timestamp}_{$migrationFileName}")
            ->first();
    }
}
