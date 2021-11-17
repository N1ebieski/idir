<?php

namespace N1ebieski\IDir\Crons\Dir;

use N1ebieski\IDir\Models\DirStatus;
use N1ebieski\IDir\Jobs\Dir\CheckStatusJob;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Carbon;

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
    protected $checkDays;

    /**
     * [protected description]
     * @var int
     */
    protected $maxAttempts;

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
        $this->maxAttempts = (int)$this->config->get('idir.dir.status.max_attempts');
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isStatusCheckerTurnOn(): bool
    {
        return $this->checkDays > 0 && $this->maxAttempts > 0;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function makeCheckTimestamp(): string
    {
        return $this->carbon->now()->subDays($this->checkDays);
    }

    /**
     * [__invoke description]
     */
    public function __invoke(): void
    {
        if (!$this->isStatusCheckerTurnOn()) {
            return;
        }

        $this->dirStatus->makeRepo()->chunkAvailableHasUrlByAttemptedAt(
            function ($dirStatuses) {
                $dirStatuses->each(function ($dirStatus) {
                    $this->addToQueue($dirStatus);
                });
            },
            $this->makeCheckTimestamp()
        );
    }

    /**
     * Undocumented function
     *
     * @param DirStatus $dirStatus
     * @return void
     */
    protected function addToQueue(DirStatus $dirStatus): void
    {
        $this->checkStatusJob->dispatch($dirStatus);
    }
}
