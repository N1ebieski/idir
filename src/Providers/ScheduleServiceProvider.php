<?php

namespace N1ebieski\IDir\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

/**
 * [ScheduleServiceProvider description]
 */
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

            $schedule->call($this->app->make(\N1ebieski\IDir\Crons\Dir\ReminderCron::class))
                ->name('ReminderCron')
                ->weekly()
                ->runInBackground();

            $schedule->call($this->app->make(\N1ebieski\IDir\Crons\Dir\CompletedCron::class))
                ->name('CompletedCron')
                ->daily()
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

            // $schedule->call($this->app->make(\N1ebieski\IDir\Crons\Tag\Dir\PopularTagsCron::class))
            //     ->name('Dir.PopularTagsCron')
            //     ->daily()
            //     ->runInBackground();

            $schedule->command('clean:directories')
                ->name('CleanDirectories')
                ->hourly()
                ->runInBackground();
        });
    }
}
