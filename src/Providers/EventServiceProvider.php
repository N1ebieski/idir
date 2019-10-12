<?php

namespace N1ebieski\IDir\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * [EventServiceProvider description]
 */
class EventServiceProvider extends ServiceProvider
{

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \N1ebieski\IDir\Events\DirStore::class => [
            // \N1ebieski\IDir\Listeners\ClearDirSession::class
            \N1ebieski\IDir\Listeners\CheckoutDir::class
        ],
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
