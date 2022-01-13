<?php

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
            $this->schedule = $this->app->make(Schedule::class);

            $this->schedule->call($this->app->make(\N1ebieski\IDir\Crons\Dir\BacklinkCron::class))
                ->name('BacklinkCron')
                ->daily()
                ->runInBackground();

            $this->schedule->call($this->app->make(\N1ebieski\IDir\Crons\Dir\CompletedCron::class))
                ->name('CompletedCron')
                ->daily()
                ->runInBackground();

            $this->callReminderSchedule();

            $this->schedule->call($this->app->make(\N1ebieski\IDir\Crons\Dir\StatusCron::class))
                ->name('StatusCron')
                ->daily()
                ->runInBackground();

            $this->schedule->call($this->app->make(\N1ebieski\IDir\Crons\Dir\ModeratorNotificationCron::class))
                ->name('ModeratorNotificationCron')
                ->hourly()
                ->runInBackground();

            $this->schedule->call($this->app->make(\N1ebieski\IDir\Crons\Sitemap\SitemapCron::class))
                ->name('SitemapCron')
                ->daily()
                ->runInBackground();
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
            ->cron("0 0 */{$days} * *")
            ->runInBackground();
    }
}
