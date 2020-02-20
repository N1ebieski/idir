<?php

namespace N1ebieski\IDir\Repositories;

use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\DirStatus;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Config\Repository as Config;
use Carbon\Carbon;
use Closure;

/**
 * [DirStatusRepo description]
 */
class DirStatusRepo
{
    /**
     * [private description]
     * @var DirStatus
     */
    protected $dirStatus;

    /**
     * [__construct description]
     * @param DirStatus $dirStatus [description]
     * @param Config   $config   [description]
     */
    public function __construct(DirStatus $dirStatus, Config $config)
    {
        $this->dirStatus = $dirStatus;

        $this->config = $config;
    }

    /**
     * [attemptNow description]
     * @return bool [description]
     */
    public function attemptedNow() : bool
    {
        return $this->dirStatus->update(['attempted_at' => Carbon::now()]);
    }

    /**
     * [resetAttempts description]
     * @return bool [description]
     */
    public function resetAttempts() : bool
    {
        return $this->dirStatus->update(['attempts' => 0]);
    }

    /**
     * [incrementAttempts description]
     * @return int [description]
     */
    public function incrementAttempts() : int
    {
        return $this->dirStatus->increment('attempts');
    }

    /**
     * Undocumented function
     *
     * @param Closure $closure
     * @return bool
     */
    public function chunkAvailableHasUrl(Closure $closure) : bool
    {
        return $this->dirStatus
            ->whereHas('dir', function ($query) {
                $query->whereIn('status', [1, 4])
                    ->whereNotNull('url');
            })
            ->where(function ($query) {
                $query->whereDate(
                    'attempted_at',
                    '<',
                    Carbon::now()->subDays($this->config->get('idir.dir.status.check_days'))->format('Y-m-d')
                )
                ->orWhere(function ($query) {
                    $query->whereDate(
                        'attempted_at',
                        '=',
                        Carbon::now()->subDays(
                            $this->config->get('idir.dir.status.check_days')
                        )->format('Y-m-d')
                    )
                    ->whereTime(
                        'attempted_at',
                        '<=',
                        Carbon::now()->subDays(
                            $this->config->get('idir.dir.status.check_days')
                        )->format('H:i:s')
                    );
                });
            })
            ->orWhere('attempted_at', null)
            ->orderBy('attempted_at', 'asc')
            ->chunk(1000, $closure);
    }
}
