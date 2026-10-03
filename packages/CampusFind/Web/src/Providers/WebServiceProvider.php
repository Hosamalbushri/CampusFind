<?php

namespace CampusFind\Web\Providers;

use Illuminate\Support\ServiceProvider;

class WebServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        if (file_exists(__DIR__ . '/../Config/web.php')) {
            $this->mergeConfigFrom(
                __DIR__ . '/../Config/web.php',
                'web'
            );
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (is_dir(__DIR__ . '/../Resources/lang')) {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'web');
        }

        if (is_dir(__DIR__ . '/../Resources/views')) {
            $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'web');
        }

        if (file_exists(__DIR__ . '/../Routes/web.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        }

        if (file_exists(__DIR__ . '/../Routes/api.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        }

        if (is_dir(__DIR__ . '/../Database/Migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        }

        if ($this->app->runningInConsole() && is_dir(__DIR__ . '/../Console/Commands')) {
            $commands = [];
            foreach (glob(__DIR__ . '/../Console/Commands/*.php') as $file) {
                $commands[] = 'CampusFind\Web\\Console\\Commands\\' . basename($file, '.php');
            }
            if ($commands !== []) {
                $this->commands($commands);
            }
        }
    }
}
