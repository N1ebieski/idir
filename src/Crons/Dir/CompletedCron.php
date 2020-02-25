<?php

namespace N1ebieski\IDir\Crons\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Carbon;
use N1ebieski\IDir\Jobs\Dir\CompletedJob;

class CompletedCron
{
    /**
     * [private description]
     * @var Dir
     */
    protected $dir;

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
     * Undocumented function
     *
     * @param Dir $dir
     * @param Config $config
     * @param Carbon $carbon
     */
    public function __construct(Dir $dir, Config $config, Carbon $carbon)
    {
        $this->dir = $dir;

        $this->config = $config;
        $this->carbon = $carbon;
    }

    /**
     * [__invoke description]
     */
    public function __invoke() : void
    {
        $this->dir->makeRepo()->chunkAvailableHasPaidRequirementByPrivilegedTo(
            function ($dirs) {
                $dirs->each(function ($dir) {
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
    protected function addToQueue(Dir $dir) : void
    {
        CompletedJob::dispatch($dir);
    }
}
