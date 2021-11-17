<?php

namespace N1ebieski\IDir\Jobs\Dir;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use N1ebieski\IDir\Repositories\DirRepo;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
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
     * @var Dir
     */
    protected $dir;

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
     * @var Event
     */
    protected $event;

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
    protected function verify(): bool
    {
        return $this->dir->isActive() && (
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
        $this->dirRepo = $this->dir->makeRepo();

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
    protected function executeDeactivation(): void
    {
        $this->dirRepo->deactivateByPayment();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function executeAltGroup(): void
    {
        $this->dir->setRelations([
                'group' => $this->dir->group()->make()->find($this->dir->group->alt_id)
            ])
            ->makeService()
            ->moveToAltGroup();
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
