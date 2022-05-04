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
