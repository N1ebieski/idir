<?php

namespace N1ebieski\IDir\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'N1ebieski\IDir\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->app['router']->bind('payment_dir_pending', function ($value) {
            return $this->app->make(\N1ebieski\IDir\Models\Payment\Dir\Payment::class)
                ->makeRepo()->firstPendingByUuid($value) ?? abort(404);
        });

        $this->app['router']->bind('category_dir_cache', function ($value) {
            return $this->app->make(\N1ebieski\IDir\Models\Category\Dir\Category::class)
                ->makeCache()->rememberBySlug($value) ?? abort(404);
        });

        $this->app['router']->bind('region_cache', function ($value) {
            return $this->app->make(\N1ebieski\IDir\Models\Region\Region::class)
                ->makeCache()->rememberBySlug($value) ?? abort(404);
        });
        
        $this->app['router']->bind('dir_cache', function ($value) {
            return $this->app->make(\N1ebieski\IDir\Models\Dir::class)
                ->makeCache()->rememberBySlug($value) ?? abort(404);
        });

        $this->app['router']->bind('stat_dir_cache', function ($value) {
            if ($this->app->make(\N1ebieski\ICore\Utils\MigrationUtil::class)
            ->contains('create_stats_table')) {
                return $this->app->make(\N1ebieski\IDir\Models\Stat\Dir\Stat::class)
                    ->makeCache()->rememberBySlug($value)
                    ?? $this->app->abort(404);
            }

            $this->app->abort(404);
        });
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapAdminRoutes();
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
        $this->app['router']->middleware(['idir.web', 'icore.force.verified'])
             ->as('web.')
             ->namespace($this->namespace.'\Web')
             ->group(function ($router) {
                foreach (glob(__DIR__ . '/../../routes/web/*.php') as $filename) {
                    if (file_exists($override = base_path('routes') . '/vendor/idir/web/' . basename($filename))) {
                        require($override);
                    } else {
                        require($filename);
                    }
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
        $this->app['router']->middleware('idir.api')
            ->prefix('api')
            ->as('api.')
            ->namespace($this->namespace.'\Api')
            ->group(function ($router) {
                foreach (glob(__DIR__ . '/../../routes/api/*.php') as $filename) {
                    if (file_exists($override = base_path('routes') . '/vendor/idir/api/' . basename($filename))) {
                        require($override);
                    } else {
                        require($filename);
                    }
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
        $this->app['router']->middleware([
                'idir.web',
                'auth',
                'verified',
                'permission:admin.access'
            ])
            ->prefix('admin')
            ->as('admin.')
            ->namespace($this->namespace.'\Admin')
            ->group(function ($router) {
                foreach (glob(__DIR__ . '/../../routes/admin/*.php') as $filename) {
                    if (file_exists($override = base_path('routes') . '/vendor/idir/admin/' . basename($filename))) {
                        require($override);
                    } else {
                        require($filename);
                    }
                }
            });
    }
}
