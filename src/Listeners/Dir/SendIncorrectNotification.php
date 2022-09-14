<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Listeners\Dir;

use Illuminate\Contracts\Mail\Mailer;
use N1ebieski\IDir\Mail\Dir\IncorrectMail;
use Illuminate\Contracts\Foundation\Application as App;
use N1ebieski\IDir\Events\Interfaces\Dir\ReasonEventInterface;
use N1ebieski\IDir\Events\Interfaces\Dir\DirEventInterface;
use Illuminate\Contracts\Debug\ExceptionHandler as Exception;

class SendIncorrectNotification
{
    /**
     * Undocumented variable
     *
     * @var DirEventInterface&ReasonEventInterface
     */
    protected $event;

    /**
     * Undocumented function
     *
     * @param Mailer $mailer
     * @param App $app
     * @param Exception $exception
     */
    public function __construct(
        protected Mailer $mailer,
        protected App $app,
        protected Exception $exception
    ) {
        //
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
     * @param  DirEventInterface&ReasonEventInterface  $event
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
