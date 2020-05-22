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
        $this->app->bind(\N1ebieski\IDir\Http\Requests\Api\Payment\Interfaces\VerifyRequestStrategy::class, function ($app) {
            switch ($this->app['config']['idir.payment.transfer.driver']) {
                case 'cashbill':
                    return new \N1ebieski\IDir\Http\Requests\Api\Payment\Cashbill\VerifyRequest;
            }
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
