<?php

namespace N1ebieski\IDir\Listeners\Dir;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;
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
     * Undocumented variable
     *
     * @var Collection
     */
    protected Collection $dirs;

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected int $counter;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(User $user, Dir $dir)
    {
        $this->user = $user;
        $this->dir = $dir;

        $this->counter = (int)Config::get('idir.dir.notification.dirs');
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
        return Cache::get('dir.notification.dirs') >= $this->counter;
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
        Mail::send(App::make(ModeratorMail::class, [
            'user' => $user,
            'dirs' => $this->dirs
        ]));
    }
}
