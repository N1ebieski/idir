<?php

namespace N1ebieski\IDir\Crons\Dir;

use Illuminate\Support\Carbon;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Jobs\Dir\ReminderJob;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Config\Repository as Config;

class ReminderCron
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
     * Undocumented variable
     *
     * @var App
     */
    protected $app;

    /**
     * Undocumented variable
     *
     * @var ReminderJob
     */
    protected $reminderJob;

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected $left_days;

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param ReminderJob $reminderJob
     * @param Config $config
     * @param App $app
     * @param Carbon $carbon
     */
    public function __construct(
        Dir $dir,
        ReminderJob $reminderJob,
        Config $config,
        App $app,
        Carbon $carbon
    ) {
        $this->dir = $dir;

        $this->reminderJob = $reminderJob;

        $this->config = $config;
        $this->app = $app;
        $this->carbon = $carbon;

        $this->left_days = (int)$this->config->get('idir.dir.reminder.left_days');
    }

    /**
     * [__invoke description]
     */
    public function __invoke(): void
    {
        $this->dir->makeRepo()->chunkAvailableHasPaidRequirementByPrivilegedTo(
            function ($dirs) {
                $dirs->each(function ($dir) {
                    $this->addToQueue($dir);
                });
            },
            $this->makeReminderTimestamp()
        );
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function makeReminderTimestamp(): string
    {
        return $this->carbon->now()->addDays($this->left_days);
    }

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @return void
     */
    protected function addToQueue(Dir $dir): void
    {
        $this->reminderJob->dispatch($dir);
    }
}
