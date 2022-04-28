<?php

namespace N1ebieski\IDir\Listeners\Dir;

use Illuminate\Contracts\Mail\Mailer;
use N1ebieski\IDir\Mail\Dir\IncorrectMail;
use Illuminate\Contracts\Foundation\Application as App;
use Illuminate\Contracts\Debug\ExceptionHandler as Exception;

class SendIncorrectNotification
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
     * @var Exception
     */
    protected $exception;

    /**
     * Undocumented function
     *
     * @param Mailer $mailer
     * @param App $app
     * @param Exception $exception
     */
    public function __construct(Mailer $mailer, App $app, Exception $exception)
    {
        $this->mailer = $mailer;
        $this->app = $app;
        $this->exception = $exception;
    }

    /**
     *
     * @return bool
     */
    public function verify(): bool
    {
        return $this->event->dir->status->isIncorrectInactive()
            && optional($this->event->dir->user)->email
            && optional($this->event->dir->user)->hasPermissionTo('web.dirs.notification');
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

        $this->updateNotes();

        try {
            $this->mailer->send($this->app->make(IncorrectMail::class, [
                'dir' => $event->dir,
                'reason' => $event->reason
            ]));
        } catch (\Throwable $e) {
            $this->exception->report($e);
        }
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function updateNotes(): bool
    {
        return $this->event->dir->update(['notes' => $this->event->reason]);
    }
}
