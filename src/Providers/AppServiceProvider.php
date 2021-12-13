<?php

namespace N1ebieski\IDir\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\N1ebieski\ICore\Utils\FileUtil::class, function ($app) {
            return $app->make(\N1ebieski\IDir\Utils\FileUtil::class, [
                'path' => ''
            ]);
        });

        $this->app->bind(\N1ebieski\IDir\Utils\ThumbnailUtil::class, function ($app, $with) {
            return new \N1ebieski\IDir\Utils\ThumbnailUtil(
                $app->make(\GuzzleHttp\Client::class),
                $app->make(\Illuminate\Contracts\Filesystem\Factory::class),
                $app->make(\Illuminate\Support\Carbon::class),
                $app->make(\Illuminate\Contracts\Config\Repository::class),
                $with['url'] ?? '',
                $with['disk'] ?? 'public'
            );
        });

        $this->app->singleton(\N1ebieski\IDir\Filters\Web\Category\ShowFilter::class, function () {
            return new \N1ebieski\IDir\Filters\Web\Category\ShowFilter(
                $this->app->make(\Illuminate\Http\Request::class),
                $this->app->make(\Illuminate\Support\Collection::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
