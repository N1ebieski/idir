<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Directives

        Blade::componentNamespace('N1ebieski\\IDir\\View\\Components', 'idir');

        $bladeCompiler = new \Illuminate\View\Compilers\BladeCompiler(
            $this->app->make(\Illuminate\Filesystem\Filesystem::class),
            Config::get('view.compiled')
        );

        $bladeCompiler->componentNamespace('N1ebieski\\ICore\\View\\Components', 'icore');
        $bladeCompiler->componentNamespace('N1ebieski\\IDir\\View\\Components', 'idir');

        Blade::directive('render', $this->app->make(\N1ebieski\ICore\View\Directives\RenderDirective::class, [
            'bladeCompiler' => $bladeCompiler
        ]));

        // Composers

        $layout = Config::get('idir.layout');

        View::composer($layout . '::admin.partials.sidebar', \N1ebieski\IDir\View\Composers\Admin\SidebarComposer::class);

        View::composer([
            $layout . '::web.field.partials.regions',
            $layout . '::admin.field.partials.regions',
            $layout . '::web.dir.partials.summary',
            $layout . '::admin.dir.partials.summary'
        ], \N1ebieski\IDir\View\Composers\RegionsComposer::class);

        View::composer([
            $layout . '::web.dir.partials.group',
            $layout . '::web.dir.partials.payment',
            $layout . '::admin.dir.partials.group',
            $layout . '::admin.dir.partials.payment',
            $layout . '::admin.price.partials.price'
        ], \N1ebieski\IDir\View\Composers\DriverComposer::class);
    }
}
