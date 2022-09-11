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
use N1ebieski\IDir\Models\DirStatus;
use N1ebieski\IDir\Jobs\Dir\CheckStatusJob;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Config\Repository as Config;

class StatusCron
{
    /**
     * Undocumented function
     *
     * @param DirStatus $dirStatus
     * @param CheckStatusJob $checkStatusJob
     * @param Config $config
     * @param Carbon $carbon
     */
    public function __construct(
        protected DirStatus $dirStatus,
        protected CheckStatusJob $checkStatusJob,
        protected Config $config,
        protected Carbon $carbon
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isStatusCheckerTurnOn(): bool
    {
        return $this->config->get('idir.dir.status.check_days') > 0
            && $this->config->get('idir.dir.status.max_attempts') > 0;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function getCheckTimestamp(): string
    {
        return $this->carbon->now()->subDays($this->config->get('idir.dir.status.check_days'));
    }

    /**
     * [__invoke description]
     */
    public function __invoke(): void
    {
        if (!$this->isStatusCheckerTurnOn()) {
            return;
        }

        $this->dirStatus->makeRepo()->chunkAvailableHasUrlByAttemptedAt(
            function (Collection $dirStatuses) {
                $dirStatuses->each(function (DirStatus $dirStatus) {
                    $this->addToQueue($dirStatus);
                });
            },
            $this->getCheckTimestamp()
        );
    }

    /**
     * Undocumented function
     *
     * @param DirStatus $dirStatus
     * @return void
     */
    protected function addToQueue(DirStatus $dirStatus): void
    {
        $this->checkStatusJob->dispatch($dirStatus);
    }
}
