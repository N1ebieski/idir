<?php

namespace N1ebieski\IDir\Listeners\DirBacklink;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Debug\ExceptionHandler as Exception;
use Illuminate\Contracts\Foundation\Application as App;
use Illuminate\Contracts\Mail\Mailer;
use N1ebieski\IDir\Mail\DirBacklink\BacklinkNotFoundMail;

class InvalidBacklinkNotification
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
     *
     * @var Config
     */
    protected $config;

    /**
     * Undocumented function
     *
     * @param Mailer $mailer
     * @param App $app
     * @param Exception $exception
     */
    public function __construct(Mailer $mailer, App $app, Exception $exception, Config $config)
    {
        $this->mailer = $mailer;
        $this->app = $app;
        $this->exception = $exception;
        $this->config = $config;
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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
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
