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

namespace N1ebieski\IDir\Crons\Dir;

use Illuminate\Support\Carbon;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use Illuminate\Contracts\Mail\Mailer;
use N1ebieski\IDir\Mail\Dir\ModeratorMail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Cache\Factory as CacheFactory;

class ModeratorNotificationCron
{
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
     *
     * @param User $user
     * @param Dir $dir
     * @param Config $config
     * @param Carbon $carbon
     * @param Mailer $mailer
     * @param App $app
     * @param CacheFactory $cache
     * @return void
     */
    public function __construct(
        protected User $user,
        protected Dir $dir,
        protected Config $config,
        protected Carbon $carbon,
        protected Mailer $mailer,
        protected App $app,
        CacheFactory $cache
    ) {
        $this->cache = $cache->store($config->has('cache.stores.system') ? 'system' : null);
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
        return $this->config->get('idir.dir.notification.hours') > 0;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isTimeToSend(): bool
    {
        return $this->carbon->parse($this->getCheckpoint())
            ->addHours($this->config->get('idir.dir.notification.hours'))
            ->lessThanOrEqualTo($this->carbon->now());
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function getCheckpoint(): string
    {
        return $this->cache->get(
            'dir.notification.hours',
            $this->carbon->now()->subHours($this->config->get('idir.dir.notification.hours'))
        );
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function putCheckpoint(): bool
    {
        return $this->cache->forever('dir.notification.hours', $this->carbon->now());
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function __invoke(): void
    {
        if (!$this->isNotificationTurnOn()) {
            return;
        }

        if ($this->isTimeToSend()) {
            $this->setLatestDirsForModerators(
                $this->dir->makeRepo()->getLatestForModeratorsByCreatedAt(
                    $this->getCheckpoint()
                )
            );

            if ($this->dirs->isNotEmpty()) {
                $this->user->makeRepo()->getModeratorsByNotificationDirsPermission()
                    ->each(function (User $user) {
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
    protected function sendMailToModerator(User $user): void
    {
        $this->mailer->send(
            $this->app->make(ModeratorMail::class, [
                'user' => $user,
                'dirs' => $this->dirs
            ])
        );
    }
}
