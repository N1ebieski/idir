<?php

namespace N1ebieski\IDir\Listeners\Dir;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use Illuminate\Contracts\Mail\Mailer;
use N1ebieski\IDir\Mail\Dir\ModeratorMail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Cache\Factory as Cache;
use Illuminate\Contracts\Foundation\Application as App;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Debug\ExceptionHandler as Exception;

class SendModeratorNotification
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
     * @var User
     */
    protected $user;

    /**
     * Undocumented variable
     *
     * @var Dir
     */
    protected $dir;

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
     * Undocumented variable
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Undocumented variable
     *
     * @var Config
     */
    protected $config;

    /**
     * Undocumented variable
     *
     * @var Collection
     */
    protected $dirs;

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected $counter;

    /**
     * Undocumented function
     *
     * @param User $user
     * @param Dir $dir
     * @param Mailer $mailer
     * @param App $app
     * @param Cache $cache
     * @param Config $config
     * @param Exception $exception
     */
    public function __construct(
        User $user,
        Dir $dir,
        Mailer $mailer,
        App $app,
        Cache $cache,
        Config $config,
        Exception $exception
    ) {
        $this->user = $user;
        $this->dir = $dir;

        $this->app = $app;
        $this->mailer = $mailer;
        $this->cache = $cache->store($config->has('cache.stores.system') ? 'system' : null);
        $this->exception = $exception;

        $this->counter = (int)$config->get('idir.dir.notification.dirs');
    }

    /**
     *
     * @return bool
     */
    public function verify() : bool
    {
        return $this->isNotificationTurnOn() && $this->event->dir->isUpdateStatus();
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isNotificationTurnOn() : bool
    {
        return $this->counter > 0;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isTimeToSend() : bool
    {
        return $this->cache->get('dir.notification.dirs') >= $this->counter;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function incrementCounter() : bool
    {
        return $this->cache->has('dir.notification.dirs') ?
            $this->cache->increment('dir.notification.dirs')
            : $this->cache->forever('dir.notification.dirs', 1);
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function forgetCounter() : bool
    {
        return $this->cache->forget('dir.notification.dirs');
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

        $this->incrementCounter();

        if ($this->isTimeToSend()) {
            $this->makeLatestDirsForModerators();

            if ($this->dirs->isNotEmpty()) {
                $this->user->makeRepo()->getModeratorsByNotificationDirsPermission()
                    ->each(function ($user) {
                        $this->sendMailToModerator($user);
                    });

                $this->forgetCounter();
            }
        }
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    protected function makeLatestDirsForModerators() : Collection
    {
        return $this->dirs = $this->dir->makeRepo()
            ->getLatestForModeratorsByLimit(
                $this->counter
            );
    }

    /**
     * Undocumented function
     *
     * @param User $user
     * @return void
     */
    protected function sendMailToModerator(User $user) : void
    {
        try {
            $this->mailer->send($this->app->make(ModeratorMail::class, [
                'user' => $user,
                'dirs' => $this->dirs
            ]));
        } catch (\Throwable $e) {
            $this->exception->report($e);
        }
    }
}
