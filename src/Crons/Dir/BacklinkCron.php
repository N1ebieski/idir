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
use N1ebieski\IDir\Models\DirBacklink;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Jobs\Dir\CheckBacklinkJob;
use Illuminate\Contracts\Config\Repository as Config;

class BacklinkCron
{
    /**
     * Undocumented function
     *
     * @param DirBacklink $dirBacklink
     * @param CheckBacklinkJob $checkBacklinkJob
     * @param Config $config
     * @param Carbon $carbon
     */
    public function __construct(
        protected DirBacklink $dirBacklink,
        protected CheckBacklinkJob $checkBacklinkJob,
        protected Config $config,
        protected Carbon $carbon
    ) {
        //
    }

    /**
     * [__invoke description]
     */
    public function __invoke(): void
    {
        $this->dirBacklink->makeRepo()->chunkAvailableHasBacklinkRequirementByAttemptedAt(
            function (Collection $dirBacklinks) {
                $dirBacklinks->each(function (DirBacklink $dirBacklink) {
                    $this->addToQueue($dirBacklink);
                });
            },
            $this->getCheckTimestamp()
        );
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function getCheckTimestamp(): string
    {
        return $this->carbon->now()->subHours(
            $this->config->get('idir.dir.backlink.check_hours')
        );
    }

    /**
     * Adds new jobs to the queue.
     */
    private function addToQueue(DirBacklink $dirBacklink): void
    {
        $this->checkBacklinkJob->dispatch($dirBacklink);
    }
}
