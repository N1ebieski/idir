<?php

namespace N1ebieski\IDir\Crons\Dir;

use N1ebieski\IDir\Models\DirBacklink;
use N1ebieski\IDir\Jobs\Dir\CheckBacklink;

class BacklinkCron
{
    /**
     * [private description]
     * @var DirBacklink
     */
    protected $dirBacklink;

    /**
     * [protected description]
     * @var CheckBacklink
     */
    protected $checkBacklink;

    /**
     * [__construct description]
     * @param DirBacklink     $dir     [description]
     * @param CheckBacklink $checkBacklink [description]
     */
    public function __construct(DirBacklink $dirBacklink, CheckBacklink $checkBacklink)
    {
        $this->dirBacklink = $dirBacklink;
        
        $this->checkBacklink = $checkBacklink;
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
                    $this->checkBacklink->dispatch($item);
                });
            }
        );
    }
}
