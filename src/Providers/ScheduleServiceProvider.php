<?php

namespace N1ebieski\IDir\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);

            $schedule->call($this->app->make(\N1ebieski\IDir\Crons\Dir\BacklinkCron::class))
                ->name('BacklinkCron')
                ->daily()
                ->runInBackground();

            $schedule->call($this->app->make(\N1ebieski\IDir\Crons\Dir\CompletedCron::class))
                ->name('CompletedCron')
                ->daily()
                ->runInBackground();

            // TODO #85 launch date set by IDIR_DIR_REMINDER_LEFT_DAYS as prepareClearCacheSchedule
            $schedule->call($this->app->make(\N1ebieski\IDir\Crons\Dir\ReminderCron::class))
                ->name('ReminderCron')
                ->weekly()
                ->runInBackground();

            $schedule->call($this->app->make(\N1ebieski\IDir\Crons\Dir\StatusCron::class))
                ->name('StatusCron')
                ->daily()
                ->runInBackground();

            $schedule->call($this->app->make(\N1ebieski\IDir\Crons\Dir\ModeratorNotificationCron::class))
                ->name('ModeratorNotificationCron')
                ->hourly()
                ->runInBackground();

            $schedule->call($this->app->make(\N1ebieski\IDir\Crons\Sitemap\SitemapCron::class))
                ->name('SitemapCron')
                ->daily()
                ->runInBackground();
        });
    }
}
