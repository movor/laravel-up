<?php

namespace App\Providers;

use Flugg\Responder\Contracts\Transformers\TransformerResolver;
use Illuminate\Database\Eloquent\FactoryBuilder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadEnvironmentSpecificEnvFile();

        $this->addWithoutEventsMacroToFactory();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() === 'local') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Provide way to disable model events when using factories
     *
     * @return void
     */
    protected function addWithoutEventsMacroToFactory()
    {
        FactoryBuilder::macro('withoutEvents', function () {
            $this->class::flushEventListeners();

            return $this;
        });
    }

    /**
     * Try to load additional .env file if we're not on production
     *
     * @return void
     */
    protected function loadEnvironmentSpecificEnvFile()
    {
        if (!\App::environment('production')) {
            $basePath = base_path();
            $envFile = '.' . \App::environment() . '.env';

            if (file_exists($basePath . DIRECTORY_SEPARATOR . $envFile)) {
                // Finally load env specific env file
                (new \Dotenv\Dotenv(base_path(), $envFile))->overload();
            }
        }
    }
}