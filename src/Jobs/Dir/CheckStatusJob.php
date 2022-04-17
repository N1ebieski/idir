<?php

namespace N1ebieski\IDir\Jobs\Dir;

use Throwable;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use N1ebieski\IDir\Models\DirStatus;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use N1ebieski\IDir\Repositories\DirRepo;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Repositories\DirStatusRepo;
use N1ebieski\IDir\Http\Clients\Dir\StatusClient;
use Illuminate\Contracts\Config\Repository as Config;

class CheckStatusJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * [protected description]
     * @var StatusClient
     */
    protected $client;

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
    public function getLastUrlFromRedirect(): string
    {
        $redirects = $this->client->getResponse()->getHeader(\GuzzleHttp\RedirectMiddleware::HISTORY_HEADER);

        return end($redirects);
    }

    /**
     * [isAttempt description]
     * @return bool [description]
     */
    protected function isAttempt(): bool
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
    protected function isMaxAttempt(): bool
    {
        return $this->dirStatus->attempts >= $this->max_attempts;
    }

    /**
     * [validate description]
     * @return bool [description]
     */
    protected function validateStatus(): bool
    {
        try {
            $this->client->get($this->dirStatus->dir->url);
        } catch (\N1ebieski\IDir\Exceptions\Dir\TransferException $e) {
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
    protected function prepareParkedDomains(): string
    {
        return $this->parked_domains = $this->str->escaped(implode('|', $this->parked_domains));
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isParked(): bool
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
    protected function isStatus(): bool
    {
        return $this->client->getResponse()->getStatusCode() === HttpResponse::HTTP_OK;
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
     * @param StatusClient $client
     * @param Carbon $carbon
     * @param Config $config
     * @return void
     */
    public function handle(
        StatusClient $client,
        Carbon $carbon,
        Config $config,
        Str $str
    ): void {
        $this->dirStatusRepo = $this->dirStatus->makeRepo();
        $this->dirRepo = $this->dirStatus->dir->makeRepo();

        $this->client = $client;
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
     * @param  Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        //
    }
}
