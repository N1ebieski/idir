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

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    // /**
    //  * This namespace is applied to your controller routes.
    //  *
    //  * In addition, it is set as the URL generator's root namespace.
    //  *
    //  * @var string
    //  */
    // protected $namespace = 'N1ebieski\IDir\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        Route::bind('payment_dir_pending', function (string $value) {
            return $this->app->make(\N1ebieski\IDir\Models\Payment\Dir\Payment::class)
                ->makeRepo()->firstPendingByUuid($value) ?? abort(HttpResponse::HTTP_NOT_FOUND);
        });

        Route::bind('category_dir_cache', function (string $value) {
            return $this->app->make(\N1ebieski\IDir\Models\Category\Dir\Category::class)
                ->makeCache()->rememberBySlug($value) ?? abort(HttpResponse::HTTP_NOT_FOUND);
        });

        Route::bind('region_cache', function (string $value) {
            return $this->app->make(\N1ebieski\IDir\Models\Region\Region::class)
                ->makeCache()->rememberBySlug($value) ?? abort(HttpResponse::HTTP_NOT_FOUND);
        });

        Route::bind('dir_cache', function (string $value) {
            return $this->app->make(\N1ebieski\IDir\Models\Dir::class)
                ->makeCache()->rememberBySlug($value) ?? abort(HttpResponse::HTTP_NOT_FOUND);
        });

        Route::bind('stat_dir_cache', function (string $value) {
            if (
                $this->app->make(\N1ebieski\ICore\Utils\MigrationUtil::class)
                ->contains('create_stats_table')
            ) {
                return $this->app->make(\N1ebieski\IDir\Models\Stat\Dir\Stat::class)
                    ->makeCache()->rememberBySlug($value)
                    ?? $this->app->abort(HttpResponse::HTTP_NOT_FOUND);
            }

            $this->app->abort(HttpResponse::HTTP_NOT_FOUND);
        });

        $this->routes(function () {
            $this->mapApiRoutes();

            $this->mapAdminRoutes();

            $this->mapWebRoutes();
        });
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        if (Config::get('idir.routes.web.enabled') === false) {
            return;
        }

        $router = Route::middleware(['idir.web', 'icore.force.verified'])
            ->prefix(Config::get('idir.routes.web.prefix'))
            ->as('web.');

        $router->group(function () {
            $filenames = glob(__DIR__ . '/../../routes/web/*.php') ?: [];

            foreach ($filenames as $filename) {
                if (!file_exists(base_path('routes') . '/vendor/idir/web/' . basename($filename))) {
                    require($filename);
                }
            }
        });

        $router->namespace(Config::get('idir.routes.web.namespace', $this->namespace . '\Web'))
            ->group(function () {
                $filenames = glob(base_path('routes') . '/vendor/idir/web/*.php') ?: [];

                foreach ($filenames as $filename) {
                    require($filename);
                }
            });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        if (Config::get('idir.routes.api.enabled') === false) {
            return;
        }

        $router = Route::middleware(['idir.api', 'icore.force.verified'])
            ->prefix(Config::get('idir.routes.api.prefix', 'api'))
            ->as('api.');

        $router->group(function () {
            $filenames = glob(__DIR__ . '/../../routes/api/*.php') ?: [];

            foreach ($filenames as $filename) {
                if (!file_exists(base_path('routes') . '/vendor/idir/api/' . basename($filename))) {
                    require($filename);
                }
            }
        });

        $router->namespace(Config::get('idir.routes.api.namespace', $this->namespace . '\Api'))
            ->group(function () {
                $filenames = glob(base_path('routes') . '/vendor/idir/api/*.php') ?: [];

                foreach ($filenames as $filename) {
                    require($filename);
                }
            });
    }

    /**
     * Define the "admin" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapAdminRoutes()
    {
        if (Config::get('idir.routes.admin.enabled') === false) {
            return;
        }

        $router = Route::middleware([
                'idir.web',
                'auth',
                'verified',
                'permission:admin.access'
            ])
            ->prefix(Config::get('idir.routes.admin.prefix', 'admin'))
            ->as('admin.');

        $router->group(function () {
            $filenames = glob(__DIR__ . '/../../routes/admin/*.php') ?: [];

            foreach ($filenames as $filename) {
                if (!file_exists(base_path('routes') . '/vendor/idir/admin/' . basename($filename))) {
                    require($filename);
                }
            }
        });

        $router->namespace(Config::get('idir.routes.admin.namespace', $this->namespace . '\Admin'))
            ->group(function () {
                $filenames = glob(base_path('routes') . '/vendor/idir/admin/*.php') ?: [];

                foreach ($filenames as $filename) {
                    require($filename);
                }
            });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
