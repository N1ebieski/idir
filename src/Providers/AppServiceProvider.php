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
        $this->app->bind(\N1ebieski\ICore\Models\Token\PersonalAccessToken::class, \N1ebieski\IDir\Models\Token\PersonalAccessToken::class);

        $this->app->when(\N1ebieski\ICore\Utils\File\FileUtil::class)
            ->needs('$temp_path')
            ->give('vendor/idir/temp');

        $this->app->when(\N1ebieski\IDir\Utils\Thumbnail\ThumbnailUtil::class)
            ->needs('$url')
            ->give('');

        $this->app->when(\N1ebieski\IDir\Console\Commands\Update\UpdateCommand::class)
            ->needs('$backupPath')
            ->give('backup/vendor/idir');

        $this->app->when(\N1ebieski\IDir\Console\Commands\Update\UpdateCommand::class)
            ->needs(\N1ebieski\ICore\Console\Commands\Update\SchemaFactory::class)
            ->give(function () {
                return $this->app->make(\N1ebieski\IDir\Console\Commands\Update\SchemaFactory::class);
            });

        $this->app->bind(\N1ebieski\IDir\Http\Clients\Payment\Interfaces\Codes\SmsClientInterface::class, function ($app) {
            switch ($app['config']['idir.payment.code_sms.driver']) {
                case 'cashbill':
                    return $app->make(\N1ebieski\IDir\Http\Clients\Payment\Cashbill\Codes\SMS\SmsClient::class);
            }
        });

        $this->app->bind(\N1ebieski\IDir\Http\Clients\Payment\Interfaces\Codes\TransferClientInterface::class, function ($app) {
            switch ($app['config']['idir.payment.code_transfer.driver']) {
                case 'cashbill':
                    return $app->make(\N1ebieski\IDir\Http\Clients\Payment\Cashbill\Codes\Transfer\TransferClient::class);
            }
        });

        $this->app->bind(\N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\TransferClientInterface::class, function ($app) {
            $driver = $app['request']->route('driver') ?? $app['config']['idir.payment.transfer.driver'];

            return $app->make(\N1ebieski\IDir\Http\Clients\Payment\Factories\TransferClientFactory::class)->makeClient($driver);
        });

        $this->app->bind(\N1ebieski\IDir\Http\Requests\Web\Payment\Interfaces\CompleteRequestInterface::class, function ($app) {
            $driver = $app['request']->route('driver') ?? $app['config']['idir.payment.transfer.driver'];

            return (new \N1ebieski\IDir\Http\Requests\Web\Payment\Factories\CompleteRequestFactory())->makeRequest($driver);
        });


        $this->app->bind(\N1ebieski\IDir\Http\Requests\Api\Payment\Interfaces\VerifyRequestInterface::class, function ($app) {
            $driver = $app['request']->route('driver') ?? $app['config']['idir.payment.transfer.driver'];

            return (new \N1ebieski\IDir\Http\Requests\Api\Payment\Factories\VerifyRequestFactory())->makeRequest($driver);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            \N1ebieski\ICore\Models\User::class => \N1ebieski\IDir\Models\User::class
        ]);

        $aliasLoader = \Illuminate\Foundation\AliasLoader::getInstance();

        foreach (glob(__DIR__ . '/../ValueObjects/**/*.php') as $classPath) {
            if (!preg_match('/ValueObjects\/([A-Za-z\/]+).php/', $classPath, $matches)) {
                continue;
            }

            $alias = str_replace('/', '\\', $matches[1]);

            $aliasLoader->alias($alias, 'N1ebieski\\IDir\\ValueObjects\\' . $alias);
        }
    }
}
