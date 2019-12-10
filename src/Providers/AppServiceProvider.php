<?php

namespace N1ebieski\IDir\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * [AppServiceProvider description]
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\N1ebieski\ICore\Repositories\LinkRepo::class, function($app, array $with) {
            return $this->app->make(\N1ebieski\IDir\Repositories\LinkRepo::class, $with);
        });

        $this->app->bind(\N1ebieski\ICore\Cache\LinkCache::class, function($app, array $with) {
            return $this->app->make(\N1ebieski\IDir\Cache\LinkCache::class, $with);
        });

        $this->app->bind(\N1ebieski\ICore\Models\User::class, function($app, array $with) {
            return $this->app->make(\N1ebieski\IDir\Models\User::class, $with);
        });
    }
}
