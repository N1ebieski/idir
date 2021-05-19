<?php

namespace N1ebieski\IDir\Providers;

use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \N1ebieski\IDir\Console\Commands\SEOKatalogCommand::class,
                \N1ebieski\IDir\Console\Commands\PHPLDCommand::class,
                \N1ebieski\IDir\Console\Commands\EnvCommand::class,
                \N1ebieski\IDir\Console\Commands\EnvTestingCommand::class,
                \N1ebieski\IDir\Console\Commands\InstallCommand::class
            ]);
        }
    }
}
