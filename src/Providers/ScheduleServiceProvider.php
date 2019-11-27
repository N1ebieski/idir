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
        $this->app->booted(function() {
             $schedule = $this->app->make(Schedule::class);

             $schedule->call($this->app->make(\N1ebieski\IDir\Crons\BacklinkCron::class))
                ->name('BacklinkCron')->daily();

             $schedule->command('clean:directories')->hourly();

             $schedule->command('queue:restart');
             $schedule->command('queue:work --daemon --stop-when-empty --tries=3');
         });
    }
}
