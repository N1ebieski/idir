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

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Undocumented variable
     *
     * @var Schedule
     */
    protected $schedule;

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->schedule = $this->app->make(Schedule::class);

        $this->app->booted(function () {
            $this->schedule->call($this->app->make(\N1ebieski\IDir\Crons\Dir\BacklinkCron::class))
                ->name('BacklinkCron')
                ->daily();

            $this->schedule->call($this->app->make(\N1ebieski\IDir\Crons\Dir\CompletedCron::class))
                ->name('CompletedCron')
                ->daily();

            $this->callReminderSchedule();

            $this->schedule->call($this->app->make(\N1ebieski\IDir\Crons\Dir\StatusCron::class))
                ->name('StatusCron')
                ->daily();

            $this->schedule->call($this->app->make(\N1ebieski\IDir\Crons\Dir\ModeratorNotificationCron::class))
                ->name('ModeratorNotificationCron')
                ->hourly();

            $this->schedule->call($this->app->make(\N1ebieski\IDir\Crons\Sitemap\SitemapCron::class))
                ->name('SitemapCron')
                ->daily();
        });
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function callReminderSchedule(): void
    {
        $days = Config::get('idir.dir.reminder.left_days');

        if ($days <= 0 || $days > 30) {
            return;
        }

        $this->schedule->call($this->app->make(\N1ebieski\IDir\Crons\Dir\ReminderCron::class))
            ->name('ReminderCron')
            ->cron("5 0 */{$days} * *");
    }
}
