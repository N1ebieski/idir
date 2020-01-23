<?php

namespace N1ebieski\IDir\Listeners\Dir;

use Illuminate\Support\Facades\Mail;
use N1ebieski\IDir\Mails\Dir\ActivationNotification;

/**
 * [SendActivationNotification description]
 */
class SendActivationNotification
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if (!optional($event->dir->user)->email) {
            return;
        }


        if ($event->dir->status === 1) {
            Mail::send(app()->makeWith(ActivationNotification::class, [
                'dir' => $event->dir
            ]));
        }
    }
}
