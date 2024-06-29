<?php

namespace Jetcod\Laravel\Translation\Providers;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class TranslationServiceProvider extends LaravelServiceProvider
{
    public function register() {}

    public function boot()
    {
        $this->offerPublishing();
    }

    protected function offerPublishing(): void
    {
        if (!$this->app->runningInConsole()) {
            // Refuse publishing configuration values in non-console environments
            return;
        }

        if (!function_exists('config_path') || !function_exists('database_path')) {
            // Refuse publishing configuration values in Lumen
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/translations.php' => config_path('translations.php'),
        ], 'translation-config');

        $this->publishes([
            __DIR__ . '/../Migrations/2024_06_28_000000_create_translations_table.php' => database_path('migrations/' . date('Y_m_d_His') . '_create_translations_table.php'),
        ], 'translation-migrations');
    }
}
