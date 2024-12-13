<?php

namespace N1ebieski\IDir\Listeners\DirStatus;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Debug\ExceptionHandler as Exception;
use Illuminate\Contracts\Foundation\Application as App;
use Illuminate\Contracts\Mail\Mailer;
use N1ebieski\IDir\Mail\DirStatus\ForbiddenMail;

class ForbiddenNotification
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
        return optional($this->event->dirStatus->dir->user)->email
            && optional($this->event->dirStatus->dir->user)->hasPermissionTo('web.dirs.notification')
            && $this->event->dirStatus->attempts === $this->config->get('idir.dir.status.max_attempts');
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
                $this->app->make(ForbiddenMail::class, [
                    'dirStatus' => $this->event->dirStatus
                ])
            );
        } catch (\Throwable $e) {
            $this->exception->report($e);
        }
    }
}
