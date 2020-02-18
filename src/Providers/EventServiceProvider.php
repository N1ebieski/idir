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
        \N1ebieski\IDir\Events\Admin\Dir\Destroy::class => [
            \N1ebieski\IDir\Listeners\Dir\SendDeleteNotification::class
        ],
        \N1ebieski\IDir\Events\Admin\Dir\UpdateStatus::class => [
            \N1ebieski\IDir\Listeners\Dir\Checkout::class,
            \N1ebieski\IDir\Listeners\Dir\SendActivationNotification::class
        ],
        \N1ebieski\IDir\Events\Admin\Dir\Store::class => [
            // \N1ebieski\IDir\Listeners\Dir\ClearSession::class
            \N1ebieski\IDir\Listeners\Dir\Checkout::class,
            \N1ebieski\IDir\Listeners\Dir\SendActivationNotification::class
        ],
        \N1ebieski\IDir\Events\Admin\Dir\UpdateFull::class => [
            // \N1ebieski\IDir\Listeners\ClearDirSession::class
            \N1ebieski\IDir\Listeners\Dir\Checkout::class,
            \N1ebieski\IDir\Listeners\Dir\SendActivationNotification::class
        ],
        \N1ebieski\IDir\Events\Web\Dir\Destroy::class => [
            \N1ebieski\IDir\Listeners\Dir\SendDeleteNotification::class
        ],
        \N1ebieski\IDir\Events\Web\Dir\Store::class => [
            // \N1ebieski\IDir\Listeners\Dir\ClearSession::class
            \N1ebieski\IDir\Listeners\Dir\Checkout::class,
            \N1ebieski\IDir\Listeners\Dir\SendActivationNotification::class,
            \N1ebieski\IDir\Listeners\Dir\SendModeratorNotification::class
        ],
        \N1ebieski\IDir\Events\Web\Dir\Update::class => [
            // \N1ebieski\IDir\Listeners\ClearDirSession::class
            \N1ebieski\IDir\Listeners\Dir\Checkout::class,
            \N1ebieski\IDir\Listeners\Dir\SendActivationNotification::class,
            \N1ebieski\IDir\Listeners\Dir\SendModeratorNotification::class
        ],
        \N1ebieski\IDir\Events\Web\Dir\UpdateRenew::class => [
            \N1ebieski\IDir\Listeners\Dir\Checkout::class
        ],
        \N1ebieski\IDir\Events\Admin\Payment\Dir\Store::class => [
            \N1ebieski\IDir\Listeners\Dir\MarkAsPaid::class,
            \N1ebieski\IDir\Listeners\Payment\CreateLogs::class
        ],
        \N1ebieski\IDir\Events\Web\Payment\Dir\VerifySuccessful::class => [
            \N1ebieski\IDir\Listeners\Dir\MarkAsPaid::class,
            \N1ebieski\IDir\Listeners\Dir\Checkout::class,
            \N1ebieski\IDir\Listeners\Dir\SendActivationNotification::class,
            \N1ebieski\IDir\Listeners\Dir\SendModeratorNotification::class
        ],
        \N1ebieski\IDir\Events\Web\Payment\Dir\VerifyAttempt::class => [
            \N1ebieski\IDir\Listeners\Payment\CreateLogs::class
        ],
        \N1ebieski\IDir\Events\Web\Payment\Dir\Store::class => [
            \N1ebieski\IDir\Listeners\Dir\MarkAsPaid::class,
            \N1ebieski\IDir\Listeners\Payment\CreateLogs::class
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

        \N1ebieski\IDir\Models\Rating\Dir\Rating::observe(\N1ebieski\ICore\Observers\RatingObserver::class);
        
        \N1ebieski\IDir\Models\Comment\Dir\Comment::observe(\N1ebieski\ICore\Observers\CommentObserver::class);
    }
}
