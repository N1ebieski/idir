<?php

namespace N1ebieski\IDir\Repositories;

use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\DirBacklink;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Config\Repository as Config;
use Carbon\Carbon;

/**
 * [DirBacklinkRepo description]
 */
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
    public function attemptedNow() : bool
    {
        return $this->dirBacklink->update(['attempted_at' => Carbon::now()]);
    }

    /**
     * [resetAttempts description]
     * @return bool [description]
     */
    public function resetAttempts() : bool
    {
        return $this->dirBacklink->update(['attempts' => 0]);
    }

    /**
     * [incrementAttempts description]
     * @return int [description]
     */
    public function incrementAttempts() : int
    {
        return $this->dirBacklink->increment('attempts');
    }
}
