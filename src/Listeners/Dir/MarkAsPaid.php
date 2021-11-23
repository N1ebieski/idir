<?php

namespace N1ebieski\IDir\Listeners\Dir;

class MarkAsPaid
{
    /**
     * Undocumented variable
     *
     * @var object
     */
    protected $event;

    /**
     *
     * @return bool
     */
    public function verify(): bool
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

        $event->payment->morph->makeService()->updateStatus([
            'status' => $event->payment->morph->group->apply_status
        ]);
    }
}
