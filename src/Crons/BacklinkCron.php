<?php

namespace N1ebieski\IDir\Crons;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Jobs\CheckBacklink;

/**
 * [MailingCron description]
 */
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
        $dirs = $this->dir->getRepo()->getAvailableHasBacklinkRequirement();

        if ($dirs->isNotEmpty()) {
            foreach ($dirs as $dir) {
                $this->checkBacklink->dispatch($dir->backlink);
            }
        }
    }
}
