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
        $layout = $this->app['config']->get('idir.layout');

        $this->app['view']->composer($layout . '::admin.partials.sidebar',
            function($view) {
                $view->with([
                    'dirs_inactive_count' => $this->app->make(\N1ebieski\IDir\Repositories\DirRepo::class)
                        ->countInactive()
                ]);
            });

        $this->app['view']->composer([
            $layout . '::web.category.dir.partials.sidebar',
            $layout . '::web.field.partials.regions',
            $layout . '::admin.field.partials.regions',
            $layout . '::web.dir.partials.summary',
            $layout . '::admin.dir.partials.summary'
        ],
        function($view) {
            $view->with([
                'regions' => $this->app->make(\N1ebieski\IDir\Models\Region\Region::class)
                    ->makeCache()->rememberAll()
            ]);
        });            
    }
}
