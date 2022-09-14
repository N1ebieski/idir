<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Jobs\Dir;

use Throwable;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use N1ebieski\IDir\Models\DirStatus;
use Illuminate\Queue\SerializesModels;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use N1ebieski\IDir\Services\Dir\DirService;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\IDir\Services\DirStatus\DirStatusService;
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
     * @var DirStatusService
     */
    protected $dirStatusService;

    /**
     * [protected description]
     * @var DirService
     */
    protected $dirService;

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
    public function __construct(protected DirStatus $dirStatus)
    {
        //
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function getLastUrlFromRedirect(): ?string
    {
        $redirects = $this->response->getHeader(\GuzzleHttp\RedirectMiddleware::HISTORY_HEADER);

        return end($redirects) ?: null;
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
            return preg_match('/(' . $this->getParkedDomainsAsString() . ')/', $redirect) ?
                true : false;
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
        $this->dirStatusService->resetAttempts();

        $this->dirService->activate();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function executeInvalidStatus(): void
    {
        $this->dirStatusService->incrementAttempts();

        if ($this->isMaxAttempt()) {
            $this->dirService->deactivateByStatus();
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
        $this->dirStatusService = $this->dirStatus->makeService();
        $this->dirService = $this->dirStatus->dir->makeService();

        $this->client = $client;
        $this->carbon = $carbon;
        $this->str = $str;
        $this->config = $config;

        if ($this->isAttempt()) {
            $this->dirStatusService->attemptedNow();

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
