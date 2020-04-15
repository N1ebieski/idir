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
        \N1ebieski\IDir\Events\Admin\Dir\DestroyEvent::class => [
            \N1ebieski\IDir\Listeners\Dir\SendDeletedNotification::class
        ],
        \N1ebieski\IDir\Events\Admin\Dir\UpdateStatusEvent::class => [
            \N1ebieski\IDir\Listeners\Dir\Checkout::class,
            \N1ebieski\IDir\Listeners\Dir\SendActivationNotification::class
        ],
        \N1ebieski\IDir\Events\Admin\Dir\StoreEvent::class => [
            \N1ebieski\IDir\Listeners\Dir\ClearSession::class,
            \N1ebieski\IDir\Listeners\Dir\Checkout::class,
            \N1ebieski\IDir\Listeners\Dir\SendActivationNotification::class
        ],
        \N1ebieski\IDir\Events\Admin\Dir\UpdateFullEvent::class => [
            \N1ebieski\IDir\Listeners\Dir\ClearSession::class,
            \N1ebieski\IDir\Listeners\Dir\Checkout::class,
            \N1ebieski\IDir\Listeners\Dir\SendActivationNotification::class
        ],
        \N1ebieski\IDir\Events\Web\Dir\DestroyEvent::class => [
            \N1ebieski\IDir\Listeners\Dir\SendDeletedNotification::class
        ],
        \N1ebieski\IDir\Events\Web\Dir\StoreEvent::class => [
            \N1ebieski\IDir\Listeners\Dir\ClearSession::class,
            \N1ebieski\IDir\Listeners\Dir\Checkout::class,
            \N1ebieski\IDir\Listeners\Dir\SendActivationNotification::class,
            \N1ebieski\IDir\Listeners\Dir\SendModeratorNotification::class
        ],
        \N1ebieski\IDir\Events\Web\Dir\UpdateEvent::class => [
            \N1ebieski\IDir\Listeners\Dir\ClearSession::class,
            \N1ebieski\IDir\Listeners\Dir\Checkout::class,
            \N1ebieski\IDir\Listeners\Dir\SendActivationNotification::class,
            \N1ebieski\IDir\Listeners\Dir\SendModeratorNotification::class
        ],
        \N1ebieski\IDir\Events\Web\Dir\UpdateRenewEvent::class => [
            \N1ebieski\IDir\Listeners\Dir\Checkout::class
        ],
        \N1ebieski\IDir\Events\Admin\Payment\Dir\StoreEvent::class => [
            \N1ebieski\IDir\Listeners\Dir\MarkAsPaid::class,
            \N1ebieski\IDir\Listeners\Payment\CreateLogs::class
        ],
        \N1ebieski\IDir\Events\Web\Payment\Dir\VerifySuccessfulEvent::class => [
            \N1ebieski\IDir\Listeners\Dir\MarkAsPaid::class,
            \N1ebieski\IDir\Listeners\Dir\Checkout::class,
            \N1ebieski\IDir\Listeners\Dir\SendActivationNotification::class,
            \N1ebieski\IDir\Listeners\Dir\SendModeratorNotification::class
        ],
        \N1ebieski\IDir\Events\Web\Payment\Dir\VerifyAttemptEvent::class => [
            \N1ebieski\IDir\Listeners\Payment\CreateLogs::class
        ],
        \N1ebieski\IDir\Events\Web\Payment\Dir\StoreEvent::class => [
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

        \N1ebieski\IDir\Models\Category\Dir\Category::observe(\N1ebieski\ICore\Observers\CategoryObserver::class);

        \N1ebieski\IDir\Models\Rating\Dir\Rating::observe(\N1ebieski\ICore\Observers\RatingObserver::class);
        
        \N1ebieski\IDir\Models\Comment\Dir\Comment::observe(\N1ebieski\ICore\Observers\CommentObserver::class);
    }
}
