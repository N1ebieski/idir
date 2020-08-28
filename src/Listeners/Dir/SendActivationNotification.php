<?php

namespace N1ebieski\IDir\Listeners\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\Mail\Mailer;
use N1ebieski\IDir\Mail\Dir\ActivationMail;
use Illuminate\Contracts\Foundation\Application as App;
use Tintnaingwin\EmailChecker\EmailCheckerManager as EmailChecker;

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
    protected $event;

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
     * Undocumented variable
     *
     * @var EmailChecker
     */
    protected $emailChecker;

    /**
     * Undocumented function
     *
     * @param Mailer $mailer
     * @param App $app
     * @param EmailChecker $emailChecker
     */
    public function __construct(Mailer $mailer, App $app, EmailChecker $emailChecker)
    {
        $this->mailer = $mailer;
        $this->app = $app;
        $this->emailChecker = $emailChecker;
    }

    /**
     *
     * @return bool
     */
    public function verify() : bool
    {
        return $this->event->dir->isActive()
            && optional($this->event->dir->user)->email
            && optional($this->event->dir->user)->hasPermissionTo('web.dirs.notification')
            && $this->emailChecker->check($this->event->dir->user->email);
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
