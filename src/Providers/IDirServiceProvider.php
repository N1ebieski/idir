<?php

namespace N1ebieski\IDir\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * [IDirServiceProvider description]
 */
class IDirServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.  '/../../config/idir.php', 'idir');

        $this->app->register(RouteServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'idir');

        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'idir');

        $this->publishes([
            __DIR__ . '/../../resources/lang/en' => resource_path('lang/vendor/idir/en'),
            __DIR__ . '/../../resources/lang/pl' => resource_path('lang/vendor/idir/pl'),
            __DIR__ . '/../../resources/lang/vendor/laravel' => resource_path('lang')
        ], 'idir.lang');

        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/idir'),
        ], 'idir.views');

        $this->app->make('Illuminate\Database\Eloquent\Factory')->load(base_path('database/factories') . '/vendor/idir');

        $this->publishes([
            __DIR__ . '/../../database/factories' => base_path('database/factories') . '/vendor/idir',
        ], 'idir.factories');

        $this->publishes([
            __DIR__ . '/../../database/migrations' => base_path('database/migrations') . '/vendor/idir',
        ], 'idir.migrations');

        $this->publishes([
            __DIR__ . '/../../database/seeds' => base_path('database/seeds') . '/vendor/idir',
        ], 'idir.seeds');
    }
}
