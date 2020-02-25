<?php

namespace N1ebieski\IDir\Jobs\Dir;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Repositories\DirRepo;
use N1ebieski\IDir\Mail\Dir\CompletedMail;
use Exception;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

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
    protected function verify() : bool
    {
        return $this->dir->isActive() && (
            (
                $this->dir->privileged_to !== null 
                && Carbon::parse($this->dir->privileged_to)->lessThanOrEqualTo(
                    Carbon::now()
                )
            ) || $this->dir->isNulledPrivileges()
        );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() : void
    {
        if (!$this->verify()) {
            return;
        }

        $this->dirRepo = $this->dir->makeRepo();

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
        Mail::send(app()->make(CompletedMail::class, ['dir' => $this->dir]));
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
