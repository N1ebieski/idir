<?php

namespace N1ebieski\IDir\Crons\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Carbon;
use N1ebieski\IDir\Mail\Dir\ReminderMail;

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
     * @var int
     */
    protected int $left_days;

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

        $this->left_days = (int)$this->config->get('idir.dir.reminder.left_days');
    }

    /**
     * [__invoke description]
     */
    public function __invoke() : void
    {
        $this->addToQueue();
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function makeReminderTimestamp() : string
    {
        return $this->carbon->now()->addDays($this->left_days);
    }

    /**
     * Adds new jobs to the queue.
     */
    private function addToQueue() : void
    {
        $this->dir->makeRepo()->chunkAvailableHasPaidRequirementByPrivilegedTo(
            function ($dirs) {
                $dirs->each(function ($dir) {
                    Mail::send(app()->make(ReminderMail::class, ['dir' => $dir]));
                });
            },
            $this->makeReminderTimestamp()
        );
    }
}
