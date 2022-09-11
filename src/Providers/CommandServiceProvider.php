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

use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \N1ebieski\IDir\Console\Commands\SEOKatalogCommand::class,
                \N1ebieski\IDir\Console\Commands\PHPLDCommand::class,
                \N1ebieski\IDir\Console\Commands\EnvCommand::class,
                \N1ebieski\IDir\Console\Commands\EnvTestingCommand::class,
                \N1ebieski\IDir\Console\Commands\InstallCommand::class,
                \N1ebieski\IDir\Console\Commands\Update\UpdateCommand::class,
                \N1ebieski\IDir\Console\Commands\Update\RollbackCommand::class,
            ]);
        }
    }
}
