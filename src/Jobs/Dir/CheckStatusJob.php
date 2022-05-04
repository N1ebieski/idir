<?php

namespace N1ebieski\IDir\Jobs\Dir;

use Throwable;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use N1ebieski\IDir\Models\DirStatus;
use Illuminate\Queue\SerializesModels;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Queue\InteractsWithQueue;
use N1ebieski\IDir\Repositories\DirRepo;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Repositories\DirStatusRepo;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\IDir\Http\Clients\DirStatus\DirStatusClient;

class CheckStatusJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * [protected description]
     * @var DirStatusClient
     */
    protected $client;

    /**
     * Undocumented variable
     *
     * @var ResponseInterface
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
    public function getLastUrlFromRedirect(): string
    {
        $redirects = $this->response->getHeader(\GuzzleHttp\RedirectMiddleware::HISTORY_HEADER);

        return end($redirects);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function getParkedDomainsAsString(): string
    {
        return $this->str->escaped(implode('|', $this->config->get('idir.dir.status.parked_domains')));
    }

    /**
     * [isAttempt description]
     * @return bool [description]
     */
    protected function isAttempt(): bool
    {
        return $this->dirStatus->attempted_at === null ||
            $this->carbon->parse($this->dirStatus->attempted_at)->lessThanOrEqualTo(
                $this->carbon->now()->subDays($this->config->get('idir.dir.status.check_days'))
            );
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isMaxAttempt(): bool
    {
        return $this->dirStatus->attempts >= $this->config->get('idir.dir.status.max_attempts');
    }

    /**
     * [validate description]
     * @return bool [description]
     */
    protected function validateStatus(): bool
    {
        try {
            $this->response = $this->client->show($this->dirStatus->dir->url->getValue());
        } catch (\N1ebieski\IDir\Exceptions\DirStatus\TransferException $e) {
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
     * @return boolean
     */
    protected function isParked(): bool
    {
        if (
            count($this->config->get('idir.dir.status.parked_domains')) > 0
            && $redirect = $this->getLastUrlFromRedirect()
        ) {
            return preg_match('/(' . $this->getParkedDomainsAsString() . ')/', $redirect);
        }

        return false;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isStatus(): bool
    {
        return $this->response->getStatusCode() === HttpResponse::HTTP_OK;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function executeValidStatus(): void
    {
        $this->dirStatusRepo->resetAttempts();

        $this->dirRepo->activate();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function executeInvalidStatus(): void
    {
        $this->dirStatusRepo->incrementAttempts();

        if ($this->isMaxAttempt()) {
            $this->dirRepo->deactivateByStatus();
        }
    }

    /**
     * Undocumented function
     *
     * @param DirStatusClient $client
     * @param Carbon $carbon
     * @param Config $config
     * @return void
     */
    public function handle(
        DirStatusClient $client,
        Carbon $carbon,
        Config $config,
        Str $str
    ): void {
        $this->dirStatusRepo = $this->dirStatus->makeRepo();
        $this->dirRepo = $this->dirStatus->dir->makeRepo();

        $this->client = $client;
        $this->carbon = $carbon;
        $this->str = $str;
        $this->config = $config;

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
     * @param  Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        //
    }
}
