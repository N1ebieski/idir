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

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        \N1ebieski\IDir\Events\Job\Dir\CompletedEvent::class => [
            \N1ebieski\IDir\Listeners\Dir\CompletedNotification::class
        ],
        \N1ebieski\IDir\Events\Job\DirBacklink\InvalidBacklinkEvent::class => [
            \N1ebieski\IDir\Listeners\DirBacklink\InvalidBacklinkNotification::class
        ],
        \N1ebieski\IDir\Events\Admin\DirStatus\DelayEvent::class => [
            \N1ebieski\IDir\Listeners\DirStatus\ForbiddenNotification::class
        ],
        \N1ebieski\IDir\Events\Admin\DirBacklink\DelayEvent::class => [
            \N1ebieski\IDir\Listeners\DirBacklink\ForbiddenNotification::class
        ],
        \N1ebieski\IDir\Events\Admin\Dir\DestroyEvent::class => [
            \N1ebieski\IDir\Listeners\Dir\SendDeletedNotification::class
        ],
        \N1ebieski\IDir\Events\Admin\Dir\UpdateStatusEvent::class => [
            \N1ebieski\IDir\Listeners\Dir\Checkout::class,
            \N1ebieski\IDir\Listeners\Dir\SendActivationNotification::class,
            \N1ebieski\IDir\Listeners\Dir\SendIncorrectNotification::class,
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
        \N1ebieski\IDir\Events\Web\Dir\ShowEvent::class => [
            \N1ebieski\IDir\Listeners\Stat\Dir\IncrementVisit::class
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
        \N1ebieski\IDir\Events\Api\Payment\Dir\VerifySuccessfulEvent::class => [
            \N1ebieski\IDir\Listeners\Dir\MarkAsPaid::class,
            \N1ebieski\IDir\Listeners\Dir\Checkout::class,
            \N1ebieski\IDir\Listeners\Dir\SendActivationNotification::class,
            \N1ebieski\IDir\Listeners\Dir\SendModeratorNotification::class
        ],
        \N1ebieski\IDir\Events\Api\Payment\Dir\VerifyAttemptEvent::class => [
            \N1ebieski\IDir\Listeners\Payment\CreateLogs::class
        ],
        \N1ebieski\IDir\Events\Web\Payment\Dir\StoreEvent::class => [
            \N1ebieski\IDir\Listeners\Dir\MarkAsPaid::class,
            \N1ebieski\IDir\Listeners\Payment\CreateLogs::class
        ],
        \N1ebieski\IDir\Events\Api\Dir\StoreEvent::class => [
            \N1ebieski\IDir\Listeners\Dir\Checkout::class,
            \N1ebieski\IDir\Listeners\Dir\SendActivationNotification::class,
            \N1ebieski\IDir\Listeners\Dir\SendModeratorNotification::class
        ],
        \N1ebieski\IDir\Events\Api\Dir\UpdateEvent::class => [
            \N1ebieski\IDir\Listeners\Dir\Checkout::class,
            \N1ebieski\IDir\Listeners\Dir\SendActivationNotification::class,
            \N1ebieski\IDir\Listeners\Dir\SendModeratorNotification::class
        ],
        \N1ebieski\IDir\Events\Api\Dir\DestroyEvent::class => [
            \N1ebieski\IDir\Listeners\Dir\SendDeletedNotification::class
        ],
        \N1ebieski\IDir\Events\Api\Payment\Dir\StoreEvent::class => [
            \N1ebieski\IDir\Listeners\Dir\MarkAsPaid::class,
            \N1ebieski\IDir\Listeners\Payment\CreateLogs::class
        ]
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        \N1ebieski\IDir\Listeners\Stat\Dir\IncrementView::class,
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        \N1ebieski\IDir\Models\User::observe(\N1ebieski\ICore\Observers\UserObserver::class);

        \N1ebieski\IDir\Models\Dir::observe(\N1ebieski\IDir\Observers\DirObserver::class);

        \N1ebieski\IDir\Models\Payment\Payment::observe(\N1ebieski\IDir\Observers\PaymentObserver::class);
        \N1ebieski\IDir\Models\Payment\Dir\Payment::observe(\N1ebieski\IDir\Observers\PaymentObserver::class);

        \N1ebieski\IDir\Models\Group::observe(\N1ebieski\IDir\Observers\GroupObserver::class);

        \N1ebieski\IDir\Models\Field\Field::observe(\N1ebieski\IDir\Observers\FieldObserver::class);
        \N1ebieski\IDir\Models\Field\Dir\Field::observe(\N1ebieski\IDir\Observers\FieldObserver::class);
        \N1ebieski\IDir\Models\Field\Group\Field::observe(\N1ebieski\IDir\Observers\FieldObserver::class);

        \N1ebieski\IDir\Models\Category\Dir\Category::observe(\N1ebieski\ICore\Observers\CategoryObserver::class);

        \N1ebieski\IDir\Models\Rating\Dir\Rating::observe(\N1ebieski\IDir\Observers\Rating\Dir\RatingObserver::class);

        \N1ebieski\IDir\Models\Comment\Dir\Comment::observe(\N1ebieski\ICore\Observers\CommentObserver::class);

        \N1ebieski\IDir\Models\Link::observe(\N1ebieski\ICore\Observers\LinkObserver::class);

        \N1ebieski\IDir\Models\BanValue::observe(\N1ebieski\ICore\Observers\BanValueObserver::class);
    }
}
