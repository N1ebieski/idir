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
        $this->app->bind(\N1ebieski\ICore\Http\Requests\Admin\Role\UpdateRequest::class, function($app, array $with) {
            return $this->app->make(\N1ebieski\IDir\Http\Requests\Admin\Role\UpdateRequest::class, $with);
        });

        $this->app->bind(\N1ebieski\ICore\Repositories\PermissionRepo::class, function($app, array $with) {
            return $this->app->make(\N1ebieski\IDir\Repositories\PermissionRepo::class, $with);
        });

        $this->app->bind(\N1ebieski\ICore\Repositories\UserRepo::class, function($app, array $with) {
            return $this->app->make(\N1ebieski\IDir\Repositories\UserRepo::class, $with);
        });

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
