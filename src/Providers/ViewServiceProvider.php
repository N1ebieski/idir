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

        $this->app['view']->composer(
            $layout . '::admin.partials.sidebar',
            \N1ebieski\IDir\View\Composers\Admin\SidebarComposer::class
        );

        $this->app['view']->composer(
            [
                $layout . '::web.field.partials.regions',
                $layout . '::admin.field.partials.regions',
                $layout . '::web.dir.partials.summary',
                $layout . '::admin.dir.partials.summary'
            ],
            \N1ebieski\IDir\View\Composers\RegionsComposer::class
        );
    }
}
