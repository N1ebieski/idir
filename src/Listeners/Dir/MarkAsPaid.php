<?php

namespace N1ebieski\IDir\Listeners\Dir;

/**
 * [MarkAsPaid description]
 */
class MarkAsPaid
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
        return $this->event->payment->isUnfinished()
            && $this->event->payment->morph->isPending();
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

        $event->payment->morph->update([
            'status' => $event->payment->morph->group->apply_status
        ]);
    }
}
