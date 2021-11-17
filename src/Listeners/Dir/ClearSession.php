<?php

namespace N1ebieski\IDir\Listeners\Dir;

use Illuminate\Contracts\Session\Session;

class ClearSession
{
    /**
     * Undocumented variable
     *
     * @var Session
     */
    protected $session;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $this->session->forget(['dir', 'dirId']);
    }
}
