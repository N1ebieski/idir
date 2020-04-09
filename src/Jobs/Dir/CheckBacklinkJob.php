<?php

namespace N1ebieski\IDir\Jobs\Dir;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\SerializesModels;
use N1ebieski\IDir\Models\DirBacklink;
use Illuminate\Queue\InteractsWithQueue;
use N1ebieski\IDir\Repositories\DirRepo;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use N1ebieski\IDir\Repositories\DirBacklinkRepo;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Foundation\Application as App;
use Illuminate\Contracts\Validation\Factory as Validator;
use N1ebieski\IDir\Mail\DirBacklink\BacklinkNotFoundMail;

/**
 * [CheckBacklink description]
 */
class CheckBacklinkJob implements ShouldQueue
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
     * @var DirBacklinkRepo
     */
    protected $dirBacklinkRepo;

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
     * Undocumented variable
     *
     * @var Validator
     */
    protected $validator;

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
    protected $max_attempts;

    /**
     * [protected description]
     * @var int
     */
    protected $hours;

    /**
     * Create a new job instance.
     *
     * @param DirBacklink $dirBacklink
     * @return void
     */
    public function __construct(DirBacklink $dirBacklink)
    {
        $this->dirBacklink = $dirBacklink;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function verifyNotification() : bool
    {
        return optional($this->dirBacklink->dir->user)->email
            && optional($this->dirBacklink->dir->user)->hasPermissionTo('notification dirs');
    }

    /**
     * [isAttempt description]
     * @return bool [description]
     */
    protected function isAttempt() : bool
    {
        return $this->dirBacklink->attempted_at === null ||
            $this->carbon->parse($this->dirBacklink->attempted_at)->lessThanOrEqualTo(
                $this->carbon->now()->subHours($this->hours)
            );
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isMaxAttempt() : bool
    {
        return $this->dirBacklink->attempts === $this->max_attempts;
    }

    /**
     * [validateBacklink description]
     * @return bool [description]
     */
    protected function validateBacklink() : bool
    {
        $validator = $this->validator->make(['backlink_url' => $this->dirBacklink->url], [
            'backlink_url' => $this->app->make('N1ebieski\\IDir\\Rules\\BacklinkRule', [
                'link' => $this->dirBacklink->link->url
            ])
        ]);

        return !$validator->fails();
    }

    /**
     * [sendMail description]
     */
    protected function sendMailToUser() : void
    {
        if (!$this->verifyNotification()) {
            return;
        }

        $this->mailer->send(
            $this->app->make(BacklinkNotFoundMail::class, ['dirBacklink' => $this->dirBacklink])
        );
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function executeValidBacklink() : void
    {
        $this->dirBacklinkRepo->resetAttempts();

        $this->dirRepo->activate();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function executeInvalidBacklink() : void
    {
        $this->dirBacklinkRepo->incrementAttempts();

        if ($this->isMaxAttempt()) {
            $this->dirRepo->deactivateByBacklink();

            $this->sendMailToUser();
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        Config $config,
        Validator $validator,
        Mailer $mailer,
        App $app,
        Carbon $carbon
    ) : void {
        $this->dirBacklinkRepo = $this->dirBacklink->makeRepo();
        $this->dirRepo = $this->dirBacklink->dir->makeRepo();
        
        $this->validator = $validator;
        $this->mailer = $mailer;
        $this->app = $app;
        $this->carbon = $carbon;

        $this->hours = $config->get('idir.dir.backlink.check_hours');
        $this->max_attempts = $config->get('idir.dir.backlink.max_attempts');

        if ($this->isAttempt()) {
            $this->dirBacklinkRepo->attemptedNow();

            if ($this->validateBacklink()) {
                $this->executeValidBacklink();
            } else {
                $this->executeInvalidBacklink();
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
