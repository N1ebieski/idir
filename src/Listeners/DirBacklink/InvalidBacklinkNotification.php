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

namespace N1ebieski\IDir\Listeners\DirBacklink;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Debug\ExceptionHandler as Exception;
use Illuminate\Contracts\Foundation\Application as App;
use Illuminate\Contracts\Mail\Mailer;
use N1ebieski\IDir\Events\Interfaces\DirBacklink\DirBacklinkEventInterface;
use N1ebieski\IDir\Mail\DirBacklink\BacklinkNotFoundMail;

class InvalidBacklinkNotification
{
    /**
     * Undocumented variable
     *
     * @var DirBacklinkEventInterface
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
        return optional($this->event->dirBacklink->dir->user)->email
            && optional($this->event->dirBacklink->dir->user)->hasPermissionTo('web.dirs.notification')
            && $this->event->dirBacklink->attempts === $this->config->get('idir.dir.backlink.max_attempts');
    }

    /**
     * Handle the event.
     *
     * @param  DirBacklinkEventInterface  $event
     * @return void
     */
    public function handle(DirBacklinkEventInterface $event)
    {
        $this->event = $event;

        if (!$this->verify()) {
            return;
        }

        try {
            $this->mailer->send(
                $this->app->make(BacklinkNotFoundMail::class, [
                    'dirBacklink' => $this->event->dirBacklink
                ])
            );
        } catch (\Throwable $e) {
            $this->exception->report($e);
        }
    }
}
