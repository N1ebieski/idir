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
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Group;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use N1ebieski\IDir\Services\Dir\DirService;
use N1ebieski\IDir\Events\Job\Dir\CompletedEvent;
use Illuminate\Contracts\Events\Dispatcher as Event;
use Illuminate\Contracts\Foundation\Application as App;

class CompletedJob implements ShouldQueue
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
     * Create a new job instance.
     *
     * @param Dir $dir
     * @return void
     */
    public function __construct(protected Dir $dir)
    {
        //
    }

    /**
     *
     * @return bool
     */
    protected function verify(): bool
    {
        return $this->dir->status->isActive() && (
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
     * @param App $app
     * @param Carbon $carbon
     * @param Event $event
     * @return void
     */
    public function handle(
        App $app,
        Carbon $carbon,
        Event $event
    ): void {
        $this->dirService = $this->dir->makeService();

        $this->app = $app;
        $this->carbon = $carbon;

        if (!$this->verify()) {
            return;
        }

        $event->dispatch(
            $app->make(CompletedEvent::class, ['dir' => $this->dir])
        );

        $this->executeResult();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function executeResult(): void
    {
        $this->dirService->nullablePrivileged();

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
    protected function executeDeactivation(): void
    {
        $this->dirService->deactivateByPayment();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function executeAltGroup(): void
    {
        /** @var Group */
        $group = $this->dir->group()->make();

        /** @var Group */
        $group = $group->find($this->dir->group->alt_id);

        $this->dir->makeService()->moveToAltGroup($group);
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
