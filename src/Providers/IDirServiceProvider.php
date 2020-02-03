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

        $this->app->register(AppServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->register(ViewServiceProvider::class);        
        $this->app->register(ScheduleServiceProvider::class);
        $this->app->register(CommandServiceProvider::class);

        $this->app['router']->middlewareGroup('idir.web', [
            // 'throttle:60,1',
            \N1ebieski\ICore\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \N1ebieski\IDir\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \N1ebieski\ICore\Http\Middleware\XSSProtection::class,
            \N1ebieski\ICore\Http\Middleware\ClearWhitespacesInStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
            \Nckg\Minify\Middleware\MinifyResponse::class
        ]);
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

        $this->publishes([
            __DIR__ . '/../../public/css' => public_path('css/vendor/idir'),
            __DIR__ . '/../../public/img' => public_path('img/vendor/idir'),
            __DIR__ . '/../../public/js' => public_path('js/vendor/idir'),
            // __DIR__ . '/../../public/svg' => public_path('svg/vendor/idir'),
            // __DIR__ . '/../../public/fonts/vendor' => public_path('fonts/vendor'),
            __DIR__ . '/../../public/mix-manifest.json' => public_path('mix-manifest.json')
        ], 'idir.public');

        if ($this->app->environment('local')) {
            $this->app->make('Illuminate\Database\Eloquent\Factory')->load(base_path('database/factories') . '/vendor/idir');
        }

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
