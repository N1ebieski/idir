<?php

namespace N1ebieski\IDir\Repositories;

use N1ebieski\IDir\Models\DirStatus;
use Illuminate\Contracts\Config\Repository as Config;
use Carbon\Carbon;
use Closure;
use N1ebieski\IDir\Models\Dir;

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
     * @param Closure $callback
     * @param string $timestamp
     * @return boolean
     */
    public function chunkAvailableHasUrlByAttemptedAt(Closure $callback, string $timestamp) : bool
    {
        return $this->dirStatus
            ->whereHas('dir', function ($query) {
                $query->whereIn('status', [Dir::ACTIVE, Dir::STATUS_INACTIVE])
                    ->whereNotNull('url');
            })
            ->where(function ($query) use ($timestamp) {
                $query->whereDate(
                    'attempted_at',
                    '<',
                    Carbon::parse($timestamp)->format('Y-m-d')
                )
                ->orWhere(function ($query) use ($timestamp) {
                    $query->whereDate(
                        'attempted_at',
                        '=',
                        Carbon::parse($timestamp)->format('Y-m-d')
                    )
                    ->whereTime(
                        'attempted_at',
                        '<=',
                        Carbon::parse($timestamp)->format('H:i:s')
                    );
                });
            })
            ->orWhere('attempted_at', null)
            ->orderBy('attempted_at', 'asc')
            ->chunk(1000, $callback);
    }
}
