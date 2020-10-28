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
        $this->app->bind(\GuzzleHttp\Client::class, function ($app) {
            return new \GuzzleHttp\Client([
                'headers' => [
                    'User-Agent' => 'iDir v' . $this->app['config']->get('idir.version')
                    . ' ' . parse_url($this->app['config']->get('app.url'), PHP_URL_HOST)
                ],
                'timeout' => 10.0
            ]);
        });

        $this->app->bind(\GusApi\GusApi::class, function ($app) {
            return new \GusApi\GusApi($app['config']->get('services.gus.api_key'));
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
