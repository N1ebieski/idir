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
        \N1ebieski\IDir\Events\Dir\Store::class => [
            // \N1ebieski\IDir\Listeners\ClearDirSession::class
            \N1ebieski\IDir\Listeners\CheckoutDir::class
        ],
        \N1ebieski\IDir\Events\Payment\Dir\VerifySuccessful::class => [
            \N1ebieski\IDir\Listeners\PaidDir::class,
            \N1ebieski\IDir\Listeners\CheckoutDir::class
        ],
        \N1ebieski\IDir\Events\Payment\Dir\VerifyAttempt::class => [
            \N1ebieski\IDir\Listeners\CreatePaymentLogs::class
        ],
        \N1ebieski\IDir\Events\Payment\Dir\Store::class => [
            \N1ebieski\IDir\Listeners\CreatePaymentLogs::class
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

        \N1ebieski\IDir\Models\Dir::observe(\N1ebieski\IDir\Observers\DirObserver::class);
    }
}
