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
use N1ebieski\IDir\Jobs\Dir\CompletedJob;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Config\Repository as Config;

class CompletedCron
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param CompletedJob $completedJob
     * @param Config $config
     * @param Carbon $carbon
     */
    public function __construct(
        protected Dir $dir,
        protected CompletedJob $completedJob,
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
        $this->dir->makeRepo()->chunkAvailableHasPaidRequirementByPrivilegedTo(
            function (Collection $dirs) {
                $dirs->each(function (Dir $dir) {
                    $this->addToQueue($dir);
                });
            },
            $this->carbon->now()
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
        $this->completedJob->dispatch($dir);
    }
}
