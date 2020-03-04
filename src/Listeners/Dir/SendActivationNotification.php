<?php

namespace N1ebieski\IDir\Listeners\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\Mail\Mailer;
use N1ebieski\IDir\Mail\Dir\ActivationMail;
use Illuminate\Contracts\Foundation\Application as App;

/**
 * [SendActivationNotification description]
 */
class SendActivationNotification
{
    /**
     * Undocumented variable
     *
     * @var object
     */
    protected object $event;

    /**
     * Undocumented variable
     *
     * @var Mailer
     */
    protected $mailer;

    /**
     * Undocumented variable
     *
     * @var App
     */
    protected $app;

    /**
     * Undocumented function
     *
     * @param Mailer $mailer
     * @param App $app
     */
    public function __construct(Mailer $mailer, App $app)
    {
        $this->mailer = $mailer;
        $this->app = $app;
    }

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

        if ($event->dir->status === Dir::ACTIVE) {
            $this->mailer->send($this->app->make(ActivationMail::class, [
                'dir' => $event->dir
            ]));
        }
    }
}
