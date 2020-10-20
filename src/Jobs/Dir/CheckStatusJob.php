<?php

namespace N1ebieski\IDir\Jobs\Dir;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use N1ebieski\IDir\Models\DirStatus;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use N1ebieski\IDir\Repositories\DirRepo;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use N1ebieski\IDir\Repositories\DirStatusRepo;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Str;

/**
 * [CheckStatus description]
 */
class CheckStatusJob implements ShouldQueue
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
     * Undocumented variable
     *
     * @var Carbon
     */
    protected $carbon;

    /**
     * Undocumented variable
     *
     * @var Str
     */
    protected $str;

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
    protected $days;

    /**
     * [protected description]
     * @var array
     */
    protected $parked_domains;

    /**
     * Create a new job instance.
     *
     * @param DirStatus $dirStatus
     * @return void
     */
    public function __construct(DirStatus $dirStatus)
    {
        $this->dirStatus = $dirStatus;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getLastUrlFromRedirect() : string
    {
        $redirects = $this->response->getHeader(\GuzzleHttp\RedirectMiddleware::HISTORY_HEADER);

        return end($redirects);
    }

    /**
     * [isAttempt description]
     * @return bool [description]
     */
    protected function isAttempt() : bool
    {
        return $this->dirStatus->attempted_at === null ||
            $this->carbon->parse($this->dirStatus->attempted_at)->lessThanOrEqualTo(
                $this->carbon->now()->subDays($this->days)
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
     * @return GuzzleResponse
     */
    public function makeResponse() : GuzzleResponse
    {
        return $this->response = $this->guzzle->request(
            'GET',
            $this->dirStatus->dir->url,
            [
                'allow_redirects' => ['track_redirects' => true],
                'decode_content' => false,
                'http_errors' => true,
                'verify' => false
            ]
        );
    }

    /**
     * [validate description]
     * @return bool [description]
     */
    protected function validateStatus() : bool
    {
        try {
            $this->makeResponse();
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return false;
        }

        if ($this->isParked()) {
            return false;
        }

        if (!$this->isStatus()) {
            return false;
        }

        return true;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function prepareParkedDomains() : string
    {
        return $this->parked_domains = $this->str->escaped(implode('|', $this->parked_domains));
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isParked() : bool
    {
        if (count($this->parked_domains) > 0 && $redirect = $this->getLastUrlFromRedirect()) {
            return preg_match('/(' . $this->prepareParkedDomains() . ')/', $redirect);
        }

        return false;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isStatus() : bool
    {
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
     * Undocumented function
     *
     * @param GuzzleClient $guzzle
     * @param Carbon $carbon
     * @param Config $config
     * @return void
     */
    public function handle(
        GuzzleClient $guzzle,
        Carbon $carbon,
        Config $config,
        Str $str
    ) : void {
        $this->dirStatusRepo = $this->dirStatus->makeRepo();
        $this->dirRepo = $this->dirStatus->dir->makeRepo();

        $this->guzzle = $guzzle;
        $this->carbon = $carbon;
        $this->str = $str;

        $this->days = $config->get('idir.dir.status.check_days');
        $this->max_attempts = $config->get('idir.dir.status.max_attempts');
        $this->parked_domains = $config->get('idir.dir.status.parked_domains');

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
