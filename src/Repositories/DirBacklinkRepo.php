<?php

namespace N1ebieski\IDir\Repositories;

use Closure;
use Carbon\Carbon;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\DirBacklink;
use Illuminate\Contracts\Config\Repository as Config;

class DirBacklinkRepo
{
    /**
     * [private description]
     * @var DirBacklink
     */
    protected $dirBacklink;

    /**
     * [__construct description]
     * @param DirBacklink $dirBacklink [description]
     * @param Config   $config   [description]
     */
    public function __construct(DirBacklink $dirBacklink, Config $config)
    {
        $this->dirBacklink = $dirBacklink;

        $this->config = $config;
    }

    /**
     * [attemptNow description]
     * @return bool [description]
     */
    public function attemptedNow(): bool
    {
        return $this->dirBacklink->update(['attempted_at' => Carbon::now()]);
    }

    /**
     * [resetAttempts description]
     * @return bool [description]
     */
    public function resetAttempts(): bool
    {
        return $this->dirBacklink->update(['attempts' => 0]);
    }

    /**
     * [incrementAttempts description]
     * @return int [description]
     */
    public function incrementAttempts(): int
    {
        return $this->dirBacklink->increment('attempts');
    }

    /**
     * Undocumented function
     *
     * @param Closure $closure
     * @param string $timestamp
     * @return bool
     */
    public function chunkAvailableHasBacklinkRequirementByAttemptedAt(
        Closure $closure,
        string $timestamp
    ): bool {
        return $this->dirBacklink
            ->whereHas('dir', function ($query) {
                $query->whereIn('status', [Dir::ACTIVE, Dir::BACKLINK_INACTIVE])
                    ->whereHas('group', function ($query) {
                        $query->obligatoryBacklink();
                    });
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
                })
                ->orWhere('attempted_at', null);
            })
            ->chunk(1000, $closure);
    }
}
