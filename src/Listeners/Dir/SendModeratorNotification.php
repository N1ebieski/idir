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

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use Illuminate\Contracts\Mail\Mailer;
use N1ebieski\IDir\Mail\Dir\ModeratorMail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Foundation\Application as App;
use N1ebieski\IDir\Events\Interfaces\Dir\DirEventInterface;
use Illuminate\Contracts\Debug\ExceptionHandler as Exception;

class SendModeratorNotification
{
    /**
     * Undocumented variable
     *
     * @var DirEventInterface
     */
    protected $event;

    /**
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Undocumented variable
     *
     * @var Collection
     */
    protected $dirs;

    /**
     * Undocumented function
     *
     * @param User $user
     * @param Dir $dir
     * @param Mailer $mailer
     * @param App $app
     * @param CacheFactory $cache
     * @param Config $config
     * @param Exception $exception
     */
    public function __construct(
        protected User $user,
        protected Dir $dir,
        protected Mailer $mailer,
        protected App $app,
        protected Config $config,
        protected Exception $exception,
        CacheFactory $cache
    ) {
        // @phpstan-ignore-next-line
        $this->cache = $cache->store($config->has('cache.stores.system') ? 'system' : null);
    }

    /**
     *
     * @return bool
     */
    public function verify(): bool
    {
        return $this->isNotificationTurnOn() && $this->event->dir->status->isUpdateStatus();
    }

    /**
     *
     * @param Collection $dirs
     * @return self
     */
    protected function setLatestDirsForModerators(Collection $dirs): self
    {
        $this->dirs = $dirs;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isNotificationTurnOn(): bool
    {
        return $this->config->get('idir.dir.notification.dirs') > 0;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isTimeToSend(): bool
    {
        return $this->cache->get('dir.notification.dirs') >= $this->config->get('idir.dir.notification.dirs');
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function incrementCounter(): void
    {
        // @phpstan-ignore-next-line
        $this->cache->has('dir.notification.dirs') ?
            $this->cache->increment('dir.notification.dirs')
            : $this->cache->forever('dir.notification.dirs', 1);
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function forgetCounter(): bool
    {
        return $this->cache->forget('dir.notification.dirs');
    }

    /**
     * Handle the event.
     *
     * @param  DirEventInterface  $event
     * @return void
     */
    public function handle(DirEventInterface $event)
    {
        $this->event = $event;

        if (!$this->verify()) {
            return;
        }

        $this->incrementCounter();

        if ($this->isTimeToSend()) {
            $this->setLatestDirsForModerators(
                $this->dir->makeRepo()->getLatestForModeratorsByLimit(
                    $this->config->get('idir.dir.notification.dirs')
                )
            );

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
     * @param User $user
     * @return void
     */
    protected function sendMailToModerator(User $user): void
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
