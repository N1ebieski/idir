<?php

namespace N1ebieski\IDir\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * [AppServiceProvider description]
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('crypt.thumbnail', function () {
            return new \Illuminate\Encryption\Encrypter(
                $this->app['config']['idir.dir.thumbnail.key'],
                $this->app['config']['app.cipher']
            );
        });

        $this->app->extend('translator', function () {
            return new \N1ebieski\IDir\Translation\Translator(
                $this->app['translation.loader'],
                $this->app['config']['app.locale']
            );
        });

        $this->app->bind(\N1ebieski\IDir\Utils\Payment\Interfaces\TransferUtilStrategy::class, function ($app) {
            switch ($this->app['config']['idir.payment.transfer.driver']) {
                case 'cashbill':
                    return $this->app->make(\N1ebieski\IDir\Utils\Payment\Cashbill\TransferUtil::class);
            }
        });

        $this->app->bind(\N1ebieski\IDir\Utils\Payment\Interfaces\Codes\TransferUtilStrategy::class, function ($app) {
            switch ($this->app['config']['idir.payment.code_transfer.driver']) {
                case 'cashbill':
                    return $this->app->make(\N1ebieski\IDir\Utils\Payment\Cashbill\Codes\TransferUtil::class);
            }
        });

        $this->app->bind(\N1ebieski\IDir\Utils\Payment\Interfaces\Codes\SMSUtilStrategy::class, function ($app) {
            switch ($this->app['config']['idir.payment.code_sms.driver']) {
                case 'cashbill':
                    return $this->app->make(\N1ebieski\IDir\Utils\Payment\Cashbill\Codes\SMSUtil::class);
            }
        });

        $this->app->bind(\N1ebieski\IDir\Http\Requests\Web\Payment\Interfaces\CompleteRequestStrategy::class, function ($app) {
            switch ($this->app['config']['idir.payment.transfer.driver']) {
                case 'cashbill':
                    return new \N1ebieski\IDir\Http\Requests\Web\Payment\Cashbill\CompleteRequest;
            }
        });
        
        $this->app->bind(\N1ebieski\IDir\Http\Requests\Web\Payment\Interfaces\ShowRequestStrategy::class, function ($app) {
            switch ($this->app['config']['idir.payment.transfer.driver']) {
                case 'cashbill':
                    return new \N1ebieski\IDir\Http\Requests\Web\Payment\Cashbill\ShowRequest;
            }
        });
        
        $this->app->bind(\N1ebieski\IDir\Http\Requests\Web\Payment\Interfaces\VerifyRequestStrategy::class, function ($app) {
            switch ($this->app['config']['idir.payment.transfer.driver']) {
                case 'cashbill':
                    return new \N1ebieski\IDir\Http\Requests\Web\Payment\Cashbill\VerifyRequest;
            }
        });

        $this->app->bind(\N1ebieski\ICore\Http\Requests\Admin\BanValue\IndexRequest::class, function ($app, array $with) {
            return new \N1ebieski\IDir\Http\Requests\Admin\BanValue\IndexRequest;
        });

        $this->app->bind(\N1ebieski\ICore\Http\Requests\Admin\BanValue\CreateRequest::class, function ($app, array $with) {
            return new \N1ebieski\IDir\Http\Requests\Admin\BanValue\CreateRequest;
        });

        $this->app->bind(\N1ebieski\ICore\Http\Requests\Admin\BanValue\StoreRequest::class, function ($app, array $with) {
            return new \N1ebieski\IDir\Http\Requests\Admin\BanValue\StoreRequest;
        });

        $this->app->bind(\N1ebieski\ICore\Http\Requests\Admin\Role\UpdateRequest::class, function ($app, array $with) {
            return new \N1ebieski\IDir\Http\Requests\Admin\Role\UpdateRequest;
        });

        $this->app->bind(\N1ebieski\ICore\Http\Requests\Web\Search\IndexRequest::class, function ($app, array $with) {
            return new \N1ebieski\IDir\Http\Requests\Web\Search\IndexRequest;
        });

        $this->app->bindMethod(\N1ebieski\IDir\Jobs\Tag\Dir\CachePopularTagsJob::class.'@handle', function ($job, $app) {
            return $job->handle($app->make(\N1ebieski\IDir\Models\Tag\Dir\Tag::class));
        });
    }
}
