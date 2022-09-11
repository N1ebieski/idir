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
use N1ebieski\IDir\Jobs\Dir\ReminderJob;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Config\Repository as Config;

class ReminderCron
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param ReminderJob $reminderJob
     * @param Config $config
     * @param App $app
     * @param Carbon $carbon
     */
    public function __construct(
        protected Dir $dir,
        protected ReminderJob $reminderJob,
        protected Config $config,
        protected App $app,
        protected Carbon $carbon
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function getReminderTimestamp(): string
    {
        return $this->carbon->now()->addDays(
            $this->config->get('idir.dir.reminder.left_days')
        );
    }

    /**
     * [__invoke description]
     */
    public function __invoke(): void
    {
        $this->dir->makeRepo()->chunkAvailableHasPaidRequirementByPrivilegedTo(
            function (Collection $dirs) {
                $dirs->each(function (Dir $dir) {
                    $this->addToQueue($dir);
                });
            },
            $this->getReminderTimestamp()
        );
    }

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @return void
     */
    protected function addToQueue(Dir $dir): void
    {
        $this->reminderJob->dispatch($dir);
    }
}
