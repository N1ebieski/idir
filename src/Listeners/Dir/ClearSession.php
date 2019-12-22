<?php

namespace N1ebieski\IDir\Listeners\Dir;

/**
 * [ClearSession description]
 */
class ClearSession
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
        session()->forget('dir');
    }
}
