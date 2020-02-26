<?php

namespace N1ebieski\IDir\Crons\Dir;

use N1ebieski\IDir\Models\DirStatus;
use N1ebieski\IDir\Jobs\Dir\CheckStatusJob;
use Illuminate\Support\Facades\Config;

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
     * [__construct description]
     * @param DirStatus     $dirStatus     [description]
     * @param CheckStatusJob $checkStatusJob [description]
     */
    public function __construct(DirStatus $dirStatus, CheckStatusJob $checkStatusJob)
    {
        $this->dirStatus = $dirStatus;
        
        $this->checkStatusJob = $checkStatusJob;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isStatusCheckerTurnOn() : bool
    {
        return Config::get('idir.dir.status.check_days') > 0
            && Config::get('idir.dir.status.max_attempts') > 0;
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
    private function addToQueue() : void
    {
        $this->dirStatus->makeRepo()->chunkAvailableHasUrl(
            function ($items) {
                $items->each(function ($item) {
                    $this->checkStatusJob->dispatch($item);
                });
            }
        );
    }
}
