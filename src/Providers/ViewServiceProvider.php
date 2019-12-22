<?php

namespace N1ebieski\IDir\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * [ViewServiceProvider description]
 */
class ViewServiceProvider extends ServiceProvider
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
        $this->app['view']->composer($this->app['config']->get('idir.layout') . '::admin.partials.sidebar',
            function($view) {
                $view->with([
                    'dirs_inactive_count' => $this->app->make(\N1ebieski\IDir\Repositories\DirRepo::class)
                        ->countInactive()
                ]);
            });
    }
}
