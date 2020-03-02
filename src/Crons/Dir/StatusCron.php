<?php

namespace N1ebieski\IDir\Crons\Dir;

use N1ebieski\IDir\Models\DirStatus;
use N1ebieski\IDir\Jobs\Dir\CheckStatusJob;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Carbon;

/**
 * [StatusCron description]
 */
class StatusCron
{
    /**
     * [private description]
     * @var DirStatus
     */
    protected $dirStatus;

    /**
     * [protected description]
     * @var CheckStatusJob
     */
    protected $checkStatusJob;

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
     * [protected description]
     * @var int
     */
    protected int $checkDays;

    /**
     * Undocumented function
     *
     * @param DirStatus $dirStatus
     * @param CheckStatusJob $checkStatusJob
     * @param Config $config
     * @param Carbon $carbon
     */
    public function __construct(
        DirStatus $dirStatus,
        CheckStatusJob $checkStatusJob,
        Config $config,
        Carbon $carbon
    ) {
        $this->dirStatus = $dirStatus;
        
        $this->checkStatusJob = $checkStatusJob;

        $this->config = $config;
        $this->carbon = $carbon;
        
        $this->checkDays = (int)$this->config->get('idir.dir.status.check_days');
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isStatusCheckerTurnOn() : bool
    {
        return $this->config->get('idir.dir.status.check_days') > 0
            && $this->config->get('idir.dir.status.max_attempts') > 0;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function makeCheckTimestamp() : string
    {
        return $this->carbon->now()->subDays($this->checkDays);
    }

    /**
     * [__invoke description]
     */
    public function __invoke() : void
    {
        if (!$this->isStatusCheckerTurnOn()) {
            return;
        }

        $this->addToQueue();
    }

    /**
     * Adds new jobs to the queue.
     */
    protected function addToQueue() : void
    {
        $this->dirStatus->makeRepo()->chunkAvailableHasUrlByAttemptedAt(
            function ($items) {
                $items->each(function ($item) {
                    $this->checkStatusJob->dispatch($item);
                });
            },
            $this->makeCheckTimestamp()
        );
    }
}
