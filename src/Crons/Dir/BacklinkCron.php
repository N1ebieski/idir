<?php

namespace N1ebieski\IDir\Crons\Dir;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Jobs\Dir\CheckBacklink;

class BacklinkCron
{
    /**
     * [private description]
     * @var Dir
     */
    protected $dir;

    /**
     * [protected description]
     * @var CheckBacklink
     */
    protected $checkBacklink;

    /**
     * [__construct description]
     * @param Dir     $dir     [description]
     * @param CheckBacklink $checkBacklink [description]
     */
    public function __construct(Dir $dir, CheckBacklink $checkBacklink)
    {
        $this->dir = $dir;
        
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
        $this->dir->makeRepo()->chunkAvailableHasBacklinkRequirement(
            function ($items) {
                $items->each(function ($item) {
                    $this->checkBacklink->dispatch($item);
                });
            }
        );
    }
}
