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

namespace N1ebieski\IDir\Listeners\DirStatus;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Debug\ExceptionHandler as Exception;
use Illuminate\Contracts\Foundation\Application as App;
use Illuminate\Contracts\Mail\Mailer;
use N1ebieski\IDir\Events\Interfaces\DirStatus\DirStatusEventInterface;
use N1ebieski\IDir\Mail\DirStatus\ForbiddenMail;

class ForbiddenNotification
{
    /**
     * Undocumented variable
     *
     * @var DirStatusEventInterface
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
        protected Exception $exception,
        protected Config $config
    ) {
        //
    }

    /**
     *
     * @return bool
     */
    public function verify(): bool
    {
        return optional($this->event->dirStatus->dir->user)->email
            && optional($this->event->dirStatus->dir->user)->hasPermissionTo('web.dirs.notification')
            && $this->event->dirStatus->attempts === $this->config->get('idir.dir.status.max_attempts');
    }

    /**
     * Handle the event.
     *
     * @param  DirStatusEventInterface  $event
     * @return void
     */
    public function handle(DirStatusEventInterface $event)
    {
        $this->event = $event;

        if (!$this->verify()) {
            return;
        }

        try {
            $this->mailer->send(
                $this->app->make(ForbiddenMail::class, [
                    'dirStatus' => $this->event->dirStatus
                ])
            );
        } catch (\Throwable $e) {
            $this->exception->report($e);
        }
    }
}
