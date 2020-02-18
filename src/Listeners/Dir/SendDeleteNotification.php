<?php

namespace N1ebieski\IDir\Listeners\Dir;

use Illuminate\Support\Facades\Mail;
use N1ebieski\IDir\Mail\Dir\DeleteNotification;

/**
 * [SendDeleteNotification description]
 */
class SendDeleteNotification
{
    /**
     * Undocumented variable
     *
     * @var object
     */
    protected object $event;

    /**
     *
     * @return bool
     */
    public function verify() : bool
    {
        return optional($this->event->dir->user)->email
            && optional($this->event->dir->user)->hasPermissionTo('notification dirs');
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $this->event = $event;

        if (!$this->verify()) {
            return;
        }

        Mail::send(app()->makeWith(DeleteNotification::class, [
            'dir' => $event->dir,
            'reason' => $event->reason
        ]));
    }
}
