<?php

namespace N1ebieski\IDir\Listeners\Dir;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use N1ebieski\IDir\Mail\Dir\ModeratorMail;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;

class SendModeratorNotification
{
    /**
     * Undocumented variable
     *
     * @var object
     */
    protected object $event;

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
        return Config::get('idir.dir.notification.dirs') > 0;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isTimeToSend() : bool
    {
        return Cache::get('dir.notification.dirs') >= Config::get('idir.dir.notification.dirs');
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function incrementCounter() : bool
    {
        return Cache::has('dir.notification.dirs') ?
            Cache::increment('dir.notification.dirs') :
            Cache::forever('dir.notification.dirs', 1);
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function forgetCounter() : bool
    {
        return Cache::forget('dir.notification.dirs');
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
            $dirs = $this->dir->makeRepo()->getLatestForModeratorsByLimit(
                Config::get('idir.dir.notification.dirs')
            );

            if ($dirs->isNotEmpty()) {
                $this->user->makeRepo()->getByNotificationDirsPermission()
                ->each(function ($user) use ($dirs) {
                    Mail::send(app()->make(ModeratorMail::class, [
                        'user' => $user,
                        'dirs' => $dirs
                    ]));
                });

                $this->forgetCounter();
            }
        }
    }
}
