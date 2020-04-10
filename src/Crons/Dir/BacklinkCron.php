<?php

namespace N1ebieski\IDir\Crons\Dir;

use N1ebieski\IDir\Models\DirBacklink;
use N1ebieski\IDir\Jobs\Dir\CheckBacklinkJob;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Carbon;

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
    protected $checkHours;

    /**
     * Undocumented function
     *
     * @param DirBacklink $dirBacklink
     * @param CheckBacklinkJob $checkBacklinkJob
     * @param Config $config
     * @param Carbon $carbon
     */
    public function __construct(
        DirBacklink $dirBacklink,
        CheckBacklinkJob $checkBacklinkJob,
        Config $config,
        Carbon $carbon
    ) {
        $this->dirBacklink = $dirBacklink;
        
        $this->checkBacklinkJob = $checkBacklinkJob;

        $this->config = $config;
        $this->carbon = $carbon;

        $this->checkHours = (int)$this->config->get('idir.dir.backlink.check_hours');
    }

    /**
     * [__invoke description]
     */
    public function __invoke() : void
    {
        $this->dirBacklink->makeRepo()->chunkAvailableHasBacklinkRequirementByAttemptedAt(
            function ($dirBacklinks) {
                $dirBacklinks->each(function ($dirBacklink) {
                    $this->addToQueue($dirBacklink);
                });
            },
            $this->makeCheckTimestamp()
        );
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function makeCheckTimestamp() : string
    {
        return $this->carbon->now()->subHours($this->checkHours);
    }

    /**
     * Adds new jobs to the queue.
     */
    private function addToQueue(DirBacklink $dirBacklink) : void
    {
        $this->checkBacklinkJob->dispatch($dirBacklink);
    }
}
