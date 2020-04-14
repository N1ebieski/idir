<?php

namespace N1ebieski\IDir\Crons\Dir;

use Illuminate\Support\Carbon;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\Mail\Mailer;
use N1ebieski\IDir\Mail\Dir\ReminderMail;
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
     * @var Mailer
     */
    protected $mailer;

    /**
     * Undocumented variable
     *
     * @var App
     */
    protected $app;

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
     * @param Config $config
     * @param Mailer $mailer
     * @param App $app
     * @param Carbon $carbon
     */
    public function __construct(Dir $dir, Config $config, Mailer $mailer, App $app, Carbon $carbon)
    {
        $this->dir = $dir;
        $this->config = $config;
        $this->mailer = $mailer;
        $this->app = $app;
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
    protected function addToQueue() : void
    {
        $this->dir->makeRepo()->chunkAvailableHasPaidRequirementByPrivilegedTo(
            function ($dirs) {
                $dirs->each(function ($dir) {
                    if (!$this->verifyNotification($dir)) {
                        return;
                    }
                    
                    $this->mailer->send(
                        $this->app->make(ReminderMail::class, ['dir' => $dir])
                    );
                });
            },
            $this->makeReminderTimestamp()
        );
    }

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @return boolean
     */
    protected function verifyNotification(Dir $dir) : bool
    {
        return optional($dir->user)->email
            && optional($dir->user)->hasPermissionTo('notification dirs');
    }
}
