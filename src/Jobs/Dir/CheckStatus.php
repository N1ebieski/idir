<?php

namespace N1ebieski\IDir\Jobs\Dir;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use N1ebieski\IDir\Models\DirStatus;
use N1ebieski\IDir\Repositories\DirStatusRepo;
use N1ebieski\IDir\Repositories\DirRepo;
use Carbon\Carbon;
use Exception;

/**
 * [CheckStatus description]
 */
class CheckStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * [protected description]
     * @var GuzzleClient
     */
    protected $guzzle;

    /**
     * [protected description]
     * @var GuzzleResponse
     */
    protected $response;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * [protected description]
     * @var DirStatus
     */
    protected $dirStatus;

    /**
     * [protected description]
     * @var DirStatusRepo
     */
    protected $dirStatusRepo;

    /**
     * [protected description]
     * @var DirRepo
     */
    protected $dirRepo;

    /**
     * [protected description]
     * @var int
     */
    protected $max_attempts;

    /**
     * [protected description]
     * @var int
     */
    protected $days;

    /**
     * Create a new job instance.
     *
     * @param DirStatus $dirStatus
     * @return void
     */
    public function __construct(DirStatus $dirStatus)
    {
        $this->dirStatus = $dirStatus;

        $this->days = config('idir.dir.status.check_days');
        $this->max_attempts = config('idir.dir.status.max_attempts');
    }

    /**
     * [isAttempt description]
     * @return bool [description]
     */
    protected function isAttempt() : bool
    {
        return $this->dirStatus->attempted_at === null ||
            Carbon::parse($this->dirStatus->attempted_at)->lessThanOrEqualTo(
                Carbon::now()->subDays($this->days)
            );
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isMaxAttempt() : bool
    {
        return $this->dirStatus->attempts >= $this->max_attempts;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function response() : void
    {
        $this->response = $this->guzzle->request('GET', $this->dirStatus->dir->url, [
            'http_errors' => true,
            'verify' => false
        ]);
    }

    /**
     * [validateStatus description]
     * @return bool [description]
     */
    protected function validateStatus() : bool
    {
        try {
            $this->response();
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return false;
        }

        return $this->response->getStatusCode() === 200;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function executeValidStatus() : void
    {
        $this->dirStatusRepo->resetAttempts();

        $this->dirRepo->activate();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function executeInvalidStatus() : void
    {
        $this->dirStatusRepo->incrementAttempts();

        if ($this->isMaxAttempt()) {
            $this->dirRepo->deactivateByStatus();
        }
    }

    /**
     * Execute the job.
     *
     * @param  GuzzleClient  $guzzle
     * @return void
     */
    public function handle(GuzzleClient $guzzle) : void
    {
        $this->guzzle = $guzzle;
        $this->dirStatusRepo = $this->dirStatus->makeRepo();
        $this->dirRepo = $this->dirStatus->dir->makeRepo();

        if ($this->isAttempt()) {
            $this->dirStatusRepo->attemptedNow();

            if ($this->validateStatus()) {
                $this->executeValidStatus();
            } else {
                $this->executeInvalidStatus();
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
