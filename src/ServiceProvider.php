<?php

namespace Sebastienheyd\BoilerplateEmailEditor;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // If routes file has been published, load routes from the published file
        $routesPath = base_path('routes/boilerplate-email-editor.php');
        $this->loadRoutesFrom(is_file($routesPath) ? $routesPath : __DIR__.'/routes/boilerplate-email-editor.php');

        // Load migrations, views and translations from current directory
        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'boilerplate-email-editor');
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'boilerplate-email-editor');

        // Add console commands
        if ($this->app->runningInConsole()) {
            // Publish files when calling php artisan vendor:publish
            $this->publishes([
                __DIR__.'/config' => config_path('boilerplate')
            ], ['email-editor-config']);

            $this->publishes([
                __DIR__.'/resources/views/layout' => resource_path('views/vendor/boilerplate-email-editor/layout'),
            ], 'email-editor-layout');

            $this->commands([Console\Layout::class]);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        app('boilerplate.menu.items')->registerMenuItem(Menu\BoilerplateEmailEditor::class);

        $this->mergeConfigFrom(__DIR__.'/config/email-editor.php', 'boilerplate.email-editor');

        // Storage disk for email layouts
        $disks = config('filesystems.disks');
        $disks['email-layouts'] = [
            'driver' => 'local',
            'root'   => config('boilerplate.email-editor.layouts_path'),
        ];
        config(['filesystems.disks' => $disks]);
    }
}
