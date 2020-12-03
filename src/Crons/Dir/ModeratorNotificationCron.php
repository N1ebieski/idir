<?php

namespace N1ebieski\IDir\Crons\Dir;

use Illuminate\Contracts\Mail\Mailer;
use N1ebieski\IDir\Mail\Dir\ModeratorMail;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Cache\Factory as Cache;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Database\Eloquent\Collection;

class ModeratorNotificationCron
{
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
     * @var Collection
     */
    protected $dirs;

    /**
     * Undocumented variable
     *
     * @var Carbon
     */
    protected $carbon;

    /**
     * Undocumented variable
     *
     * @var Config
     */
    protected $config;

    /**
     * Undocumented variable
     *
     * @var Cache
     */
    protected $cache;

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
     * @var int
     */
    protected $hours;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        User $user,
        Dir $dir,
        Config $config,
        Carbon $carbon,
        Cache $cache,
        Mailer $mailer,
        App $app
    ) {
        $this->user = $user;
        $this->dir = $dir;

        $this->config = $config;
        $this->carbon = $carbon;
        $this->cache = $cache;
        $this->mailer = $mailer;
        $this->app = $app;

        $this->hours = (int)$this->config->get('idir.dir.notification.hours');
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isNotificationTurnOn() : bool
    {
        return $this->hours > 0;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isTimeToSend() : bool
    {
        return $this->carbon->parse($this->getCheckpoint())
            ->addHours($this->hours)->lessThanOrEqualTo($this->carbon->now());
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function getCheckpoint() : string
    {
        return $this->cache->get(
            'dir.notification.hours',
            $this->carbon->now()->subHours($this->hours)
        );
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function putCheckpoint() : bool
    {
        return $this->cache->forever('dir.notification.hours', $this->carbon->now());
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    protected function makeLatestDirsForModerators() : Collection
    {
        return $this->dirs = $this->dir->makeRepo()->getLatestForModeratorsByCreatedAt(
            $this->getCheckpoint()
        );
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function __invoke() : void
    {
        if (!$this->isNotificationTurnOn()) {
            return;
        }

        if ($this->isTimeToSend()) {
            $this->makeLatestDirsForModerators();
    
            if ($this->dirs->isNotEmpty()) {
                $this->user->makeRepo()->getModeratorsByNotificationDirsPermission()
                    ->each(function ($user) {
                        $this->sendMailToModerator($user);
                    });
    
                $this->putCheckpoint();
            }
        }
    }

    /**
     * Undocumented function
     *
     * @param User $user
     * @return void
     */
    protected function sendMailToModerator(User $user) : void
    {
        $this->mailer->send(
            $this->app->make(ModeratorMail::class, [
                'user' => $user,
                'dirs' => $this->dirs
            ])
        );
    }
}
