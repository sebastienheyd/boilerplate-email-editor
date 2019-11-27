<?php

namespace Sebastienheyd\BoilerplateEmailEditor;

use Illuminate\Support\ServiceProvider;

class BoilerplateEmailEditorServiceProvider extends ServiceProvider
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
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        app('boilerplate.menu.items')->registerMenuItem(Menu\BoilerplateEmailEditor::class);
    }
}
