<?php

namespace N1ebieski\IDir\Listeners\Dir;

/**
 * [MarkAsPaid description]
 */
class MarkAsPaid
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if ($event->payment->status === 0) {
            if ($event->payment->morph->status === 2) {
                $event->payment->morph->update(['status' => $event->payment->morph->group->apply_status]);
            }
        }
    }
}
