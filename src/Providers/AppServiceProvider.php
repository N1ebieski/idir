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
        $this->app->bind(\N1ebieski\ICore\Utils\FileUtil::class, \N1ebieski\IDir\Utils\FileUtil::class);

        $this->app->when(\N1ebieski\IDir\Utils\ThumbnailUtil::class)
            ->needs('$url')
            ->give('');
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
