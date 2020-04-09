<?php

namespace N1ebieski\IDir\Jobs\Dir;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use N1ebieski\IDir\Repositories\DirRepo;
use N1ebieski\IDir\Mail\Dir\CompletedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Foundation\Application as App;

class CompletedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * [protected description]
     * @var Dir
     */
    protected $dir;

    /**
     * [protected description]
     * @var DirRepo
     */
    protected $dirRepo;

    /**
     * Undocumented variable
     *
     * @var Carbon
     */
    protected $carbon;

    /**
     * Undocumented variable
     *
     * @var App
     */
    protected $app;

    /**
     * Undocumented variable
     *
     * @var Mailer
     */
    protected $mailer;

    /**
     * Create a new job instance.
     *
     * @param Dir $dir
     * @return void
     */
    public function __construct(Dir $dir)
    {
        $this->dir = $dir;
    }

    /**
     *
     * @return bool
     */
    protected function verifyJob() : bool
    {
        return $this->dir->isActive() && (
            (
                $this->dir->privileged_to !== null
                && $this->carbon->parse($this->dir->privileged_to)->lessThanOrEqualTo(
                    $this->carbon->now()
                )
            ) || $this->dir->isNulledPrivileges()
        );
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function verifyNotification() : bool
    {
        return optional($this->dir->user)->email
            && optional($this->dir->user)->hasPermissionTo('notification dirs');
    }

    /**
     * Undocumented function
     *
     * @param Mailer $mailer
     * @param App $app
     * @param Carbon $carbon
     * @return void
     */
    public function handle(
        Mailer $mailer,
        App $app,
        Carbon $carbon
    ) : void {
        $this->dirRepo = $this->dir->makeRepo();

        $this->mailer = $mailer;
        $this->app = $app;
        $this->carbon = $carbon;

        if (!$this->verifyJob()) {
            return;
        }

        $this->executeResult();

        $this->sendNotification();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function executeResult() : void
    {
        $this->dirRepo->nullablePrivileged();

        if ($this->dir->group->alt_id === null) {
            $this->executeDeactivation();

            return;
        }

        $this->executeAltGroup();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function executeDeactivation() : void
    {
        $this->dirRepo->deactivateByPayment();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function executeAltGroup() : void
    {
        $this->dir->makeService()->moveToAltGroup();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function sendNotification() : void
    {
        if (!$this->verifyNotification()) {
            return;
        }

        $this->mailer->send(
            $this->app->make(CompletedMail::class, ['dir' => $this->dir])
        );
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        //
    }
}
