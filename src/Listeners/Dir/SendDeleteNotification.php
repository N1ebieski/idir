<?php

namespace N1ebieski\IDir\Listeners\Dir;

use Illuminate\Support\Facades\Mail;
use N1ebieski\IDir\Mails\Dir\DeleteNotification;

/**
 * [SendDeleteNotification description]
 */
class SendDeleteNotification
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        Mail::send(app()->makeWith(DeleteNotification::class, [
            'dir' => $event->dir,
            'reason' => $event->reason
        ]));
    }
}
