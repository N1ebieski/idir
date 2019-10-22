<?php

namespace N1ebieski\IDir\Listeners;

/**
 * [PaidDir description]
 */
class PaidDir
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
            if ($event->payment->model->status === 2) {
                $event->payment->model->update(['status' => $event->payment->model->group->apply_status]);
            }
        }
    }
}
