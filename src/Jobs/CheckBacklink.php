<?php

namespace N1ebieski\IDir\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use N1ebieski\IDir\Models\DirBacklink;
use Illuminate\Support\Facades\Mail;
use N1ebieski\IDir\Mails\BacklinkNotFound;
use Validator;
use Carbon\Carbon;
use Exception;

/**
 * [CheckBacklink description]
 */
class CheckBacklink implements ShouldQueue
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
     * @var DirBacklink
     */
    protected $dirBacklink;

    /**
     * [protected description]
     * @var string
     */
    protected $hours;

    /**
     * [protected description]
     * @var int
     */
    protected $max_attempts;

    /**
     * Create a new job instance.
     *
     * @param DirBacklink $dirBacklink
     * @return void
     */
    public function __construct(DirBacklink $dirBacklink)
    {
        $this->dirBacklink = $dirBacklink;

        $this->hours = config('idir.dir.backlink.check_hours');
        $this->max_attempts = config('idir.dir.backlink.max_attempts');
    }

    /**
     * [isAttempt description]
     * @return bool [description]
     */
    protected function isAttempt() : bool
    {
        return $this->dirBacklink->attempted_at === null ||
            Carbon::parse($this->dirBacklink->attempted_at)->lessThanOrEqualTo(
                Carbon::now()->subHours($this->hours)
            );
    }

    /**
     * [validateBacklink description]
     * @return bool [description]
     */
    protected function validateBacklink() : bool
    {
        $validator = Validator::make(['backlink_url' => $this->dirBacklink->url], [
            'backlink_url' => app()->make('N1ebieski\\IDir\\Rules\\Backlink', [
                'link' => $this->dirBacklink->link->url
            ])
        ]);

        return $validator->fails();
    }

    /**
     * [sendMail description]
     */
    protected function sendMailToUser() : void
    {
        if ($this->dirBacklink->attempts === $this->max_attempts) {
            Mail::send(app()->makeWith(BacklinkNotFound::class, ['dirBacklink' => $this->dirBacklink]));
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() : void
    {
        if ($this->isAttempt()) {
            $this->dirBacklink->getRepo()->attemptedNow();

            if ($this->validateBacklink()) {
                $this->dirBacklink->getRepo()->incrementAttempts();

                $this->dirBacklink->dir->getRepo()->deactivateByBacklink();

                $this->sendMailToUser();
            } else {
                $this->dirBacklink->getRepo()->resetAttempts();

                $this->dirBacklink->dir->getRepo()->activate();
            }
        }
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
