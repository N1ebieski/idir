<?php

namespace N1ebieski\IDir\Crons\Dir;

use N1ebieski\IDir\Models\DirStatus;
use N1ebieski\IDir\Jobs\Dir\CheckStatus;
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
     * @var CheckStatus
     */
    protected $checkStatus;

    /**
     * [__construct description]
     * @param DirStatus     $dirStatus     [description]
     * @param CheckStatus $checkStatus [description]
     */
    public function __construct(DirStatus $dirStatus, CheckStatus $checkStatus)
    {
        $this->dirStatus = $dirStatus;
        
        $this->checkStatus = $checkStatus;
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
                    $this->checkStatus->dispatch($item);
                });
            }
        );
    }
}
