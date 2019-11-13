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

        $this->app['router']->bind('group_dir_available', function($value) {
            return $this->app->make(\N1ebieski\IDir\Models\Group::class)->makeRepo()
                ->firstPublicById($value) ?? abort(404);
        });

        $this->app['router']->bind('payment_dir_pending', function($value) {
            return $this->app->make(\N1ebieski\IDir\Models\Payment\Dir\Payment::class)
                ->makeRepo()->firstPendingById($value) ?? abort(404);
        });
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        // $this->mapApiRoutes();

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

    // /**
    //  * Define the "api" routes for the application.
    //  *
    //  * These routes are typically stateless.
    //  *
    //  * @return void
    //  */
    // protected function mapApiRoutes()
    // {
    //     Route::prefix('api')
    //          ->middleware('api')
    //          ->namespace($this->namespace)
    //          ->group(function ($router) {
    //              if (file_exists($override = base_path('routes') . '/vendor/icore/api.php')) {
    //                  require($override);
    //              } else {
    //                  require(__DIR__ . '/../../routes/api.php');
    //              }
    //          });
    // }

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
                'permission:access admin'
            ])
            ->prefix('admin')
            ->as('admin.')
            ->namespace($this->namespace.'\Admin')
            ->group(function ($router) {
                foreach (glob(__DIR__ . '/../../routes/admin/*.php') as $filename){
                    if (file_exists($override = base_path('routes') . '/vendor/idir/admin/' . basename($filename))) {
                        require($override);
                    } else {
                        require($filename);
                    }
                }
            });
    }
}
