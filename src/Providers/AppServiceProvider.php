<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use N1ebieski\IDir\ValueObjects\Thumbnail\Driver;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\IDir\Http\Clients\Thumbnail\Factories\ThumbnailClientFactory;
use N1ebieski\IDir\Http\Clients\Thumbnail\Interfaces\ThumbnailClientInterface;

/**
 * @property-read Application $app
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ThumbnailClientInterface::class, function (Application $app) {
            /** @var Config $config */
            $config = $app->make(Config::class);

            /** @var Driver $driver */
            $driver = $config->get('idir.dir.thumbnail.driver');

            /** @var ThumbnailClientFactory $factory */
            $factory = $app->make(ThumbnailClientFactory::class);

            return $factory->makeClient($driver);
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
