<?php

namespace N1ebieski\IDir\Crons\Dir;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use N1ebieski\IDir\Mail\Dir\ModeratorNotification;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use Carbon\Carbon;

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
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(User $user, Dir $dir)
    {
        $this->user = $user;
        $this->dir = $dir;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isNotificationTurnOn() : bool
    {
        return Config::get('idir.dir.notification.hours') > 0;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isTimeToSend() : bool
    {
        return Carbon::parse($this->getCheckpoint())->addHours(
            Config::get('idir.dir.notification.hours') ?? 0
        ) < Carbon::now();
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function getCheckpoint() : string
    {
        return Cache::get(
            'dir.notification.hours',
            Carbon::now()->subHours(Config::get('idir.dir.notification.hours'))
        );
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function putCheckpoint() : bool
    {
        return Cache::forever('dir.notification.hours', Carbon::now());
    }

    /**
     * [__invoke description]
     */
    public function __invoke() : void
    {
        if (!$this->isNotificationTurnOn()) {
            return;
        }

        if ($this->isTimeToSend()) {
            $this->addToQueue();
        }
    }

    /**
     * Adds new jobs to the queue.
     */
    protected function addToQueue() : void
    {
        $dirs = $this->dir->makeRepo()->getLatestForModeratorsByCreatedAt(
            $this->getCheckpoint()
        );

        if ($dirs->isNotEmpty()) {
            $this->user->makeRepo()->getByNotificationDirsPermission()
                ->each(function ($user) use ($dirs) {
                    Mail::send(app()->make(ModeratorNotification::class, [
                        'user' => $user,
                        'dirs' => $dirs
                    ]));
                });

            $this->putCheckpoint();
        }
    }
}
