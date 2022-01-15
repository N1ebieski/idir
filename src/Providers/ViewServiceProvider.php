<?php

namespace N1ebieski\IDir\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

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
        $layout = Config::get('idir.layout');

        View::composer(
            $layout . '::admin.partials.sidebar',
            \N1ebieski\IDir\View\Composers\Admin\SidebarComposer::class
        );

        View::composer(
            [
                $layout . '::web.field.partials.regions',
                $layout . '::admin.field.partials.regions',
                $layout . '::web.dir.partials.summary',
                $layout . '::admin.dir.partials.summary'
            ],
            \N1ebieski\IDir\View\Composers\RegionsComposer::class
        );

        View::composer(
            [
                $layout . '::web.dir.partials.group',
                $layout . '::web.dir.partials.payment',
                $layout . '::admin.dir.partials.group',
                $layout . '::admin.dir.partials.payment',
                $layout . '::admin.price.partials.price'
            ],
            \N1ebieski\IDir\View\Composers\DriverComposer::class
        );
    }
}
