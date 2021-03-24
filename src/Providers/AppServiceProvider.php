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
        $this->app->bind(\N1ebieski\IDir\Utils\Payment\Interfaces\TransferUtilStrategy::class, function ($app) {
            switch ($this->app['request']->route('driver')) {
                case 'paypal':
                    return $this->app->make(\N1ebieski\IDir\Utils\Payment\PayPal\PayPalExpressAdapter::class);
            }
            
            switch ($this->app['config']['idir.payment.transfer.driver']) {
                case 'cashbill':
                    return $this->app->make(\N1ebieski\IDir\Utils\Payment\Cashbill\TransferUtil::class);
            }
        });

        $this->app->bind(\N1ebieski\IDir\Http\Requests\Web\Payment\Interfaces\CompleteRequestStrategy::class, function ($app) {
            switch ($this->app['request']->route('driver')) {
                case 'paypal':
                    return new \N1ebieski\IDir\Http\Requests\Web\Payment\PayPal\CompleteRequest;
            }

            switch ($this->app['config']['idir.payment.transfer.driver']) {
                case 'cashbill':
                    return new \N1ebieski\IDir\Http\Requests\Web\Payment\Cashbill\CompleteRequest;
            }
        });

        $this->app->bind(\N1ebieski\IDir\Http\Requests\Api\Payment\Interfaces\VerifyRequestStrategy::class, function ($app) {
            switch ($this->app['request']->route('driver')) {
                case 'paypal':
                    return new \N1ebieski\IDir\Http\Requests\Api\Payment\PayPal\VerifyRequest;
            }

            switch ($this->app['config']['idir.payment.transfer.driver']) {
                case 'cashbill':
                    return new \N1ebieski\IDir\Http\Requests\Api\Payment\Cashbill\VerifyRequest;
            }
        });

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
