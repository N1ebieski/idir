<?php

namespace N1ebieski\IDir\Crons\Dir;

use N1ebieski\IDir\Models\DirBacklink;
use N1ebieski\IDir\Jobs\Dir\CheckBacklinkJob;

class BacklinkCron
{
    /**
     * [private description]
     * @var DirBacklink
     */
    protected $dirBacklink;

    /**
     * [protected description]
     * @var CheckBacklinkJob
     */
    protected $checkBacklinkJob;

    /**
     * [__construct description]
     * @param DirBacklink     $dirBacklink     [description]
     * @param CheckBacklinkJob $checkBacklinkJob [description]
     */
    public function __construct(DirBacklink $dirBacklink, CheckBacklinkJob $checkBacklinkJob)
    {
        $this->dirBacklink = $dirBacklink;
        
        $this->checkBacklinkJob = $checkBacklinkJob;
    }

    /**
     * [__invoke description]
     */
    public function __invoke() : void
    {
        $this->addToQueue();
    }

    /**
     * Adds new jobs to the queue.
     */
    private function addToQueue() : void
    {
        $this->dirBacklink->makeRepo()->chunkAvailableHasBacklinkRequirement(
            function ($items) {
                $items->each(function ($item) {
                    $this->checkBacklinkJob->dispatch($item);
                });
            }
        );
    }
}
