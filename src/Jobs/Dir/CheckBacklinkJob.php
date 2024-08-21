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
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Queue\SerializesModels;
use N1ebieski\IDir\Models\DirBacklink;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use N1ebieski\IDir\Services\Dir\DirService;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use Illuminate\Contracts\Events\Dispatcher as Event;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Foundation\Application as App;
use Illuminate\Contracts\Validation\Factory as Validator;
use N1ebieski\IDir\Services\DirBacklink\DirBacklinkService;
use N1ebieski\IDir\Events\Job\DirBacklink\InvalidBacklinkEvent;

class CheckBacklinkJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * [protected description]
     * @var DirBacklinkService
     */
    protected $dirBacklinkService;

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
     * @var App
     */
    protected $app;

    /**
     * Undocumented variable
     *
     * @var Event
     */
    protected $event;

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
     * Create a new job instance.
     *
     * @param DirBacklink $dirBacklink
     * @return void
     */
    public function __construct(protected DirBacklink $dirBacklink)
    {
        //
    }

    /**
     * [isAttempt description]
     * @return bool [description]
     */
    protected function isAttempt(): bool
    {
        return (
            $this->dirBacklink->attempted_at === null ||
            $this->carbon->parse($this->dirBacklink->attempted_at)->lessThanOrEqualTo(
                $this->carbon->now()->subHours($this->config->get('idir.dir.backlink.check_hours'))
            )
        )
        && in_array($this->dirBacklink->dir->status->getValue(), [
            Status::ACTIVE,
            Status::BACKLINK_INACTIVE
        ]);
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isMaxAttempt(): bool
    {
        return $this->dirBacklink->attempts === $this->config->get('idir.dir.backlink.max_attempts');
    }

    /**
     * [validateBacklink description]
     * @return bool [description]
     */
    protected function validateBacklink(): bool
    {
        $validator = $this->validator->make(['backlink_url' => $this->dirBacklink->url], [
            'backlink_url' => $this->app->make(\N1ebieski\IDir\Rules\BacklinkRule::class, [
                'link' => $this->dirBacklink->link->url
            ])
        ]);

        return !$validator->fails();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function executeValidBacklink(): void
    {
        $this->dirBacklinkService->resetAttempts();

        $this->dirService->activate();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function executeInvalidBacklink(): void
    {
        $this->dirBacklinkService->incrementAttempts();

        if ($this->isMaxAttempt()) {
            $this->dirService->deactivateByBacklink();
        }
    }

    /**
     * Undocumented function
     *
     * @param Config $config
     * @param Validator $validator
     * @param Event $event
     * @param App $app
     * @param Carbon $carbon
     * @return void
     */
    public function handle(
        Config $config,
        Validator $validator,
        Event $event,
        App $app,
        Carbon $carbon
    ): void {
        $this->dirBacklinkService = $this->dirBacklink->makeService();
        $this->dirService = $this->dirBacklink->dir->makeService();

        $this->validator = $validator;
        $this->app = $app;
        $this->carbon = $carbon;
        $this->config = $config;

        if ($this->isAttempt()) {
            $this->dirBacklinkService->attemptedNow();

            if ($this->validateBacklink()) {
                $this->executeValidBacklink();
            } else {
                $this->executeInvalidBacklink();

                $event->dispatch(
                    $app->make(InvalidBacklinkEvent::class, [
                        'dirBacklink' => $this->dirBacklink
                    ])
                );
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
