<?php

namespace N1ebieski\IDir\Repositories;

use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Config\Repository as Config;
use Carbon\Carbon;

/**
 * [DirRepo description]
 */
class DirRepo
{
    /**
     * [private description]
     * @var Dir
     */
    protected $dir;

    /**
     * [protected description]
     * @var int
     */
    protected $checkBacklinkHours;

    /**
     * [__construct description]
     * @param Dir $dir [description]
     * @param Config   $config   [description]
     */
    public function __construct(Dir $dir, Config $config)
    {
        $this->dir = $dir;

        $this->config = $config;
    }

    /**
     * [getHasBacklinkRequirement description]
     * @return Collection [description]
     */
    public function getAvailableHasBacklinkRequirement() : Collection
    {
        return $this->dir->with('backlink')
            ->whereIn('status', [1, 3])
            ->whereHas('group', function($query) {
                $query->obligatoryBacklink();
            })
            ->whereHas('backlink', function($query) {
                $query->where(function($query) {
                    $query->whereDate(
                            'attempted_at',
                            '<=',
                            Carbon::now()->subHours($this->config->get('idir.dir.backlink.check_hours'))->format('Y-m-d')
                        )->whereTime(
                            'attempted_at',
                            '<=',
                            Carbon::now()->subHours($this->config->get('idir.dir.backlink.check_hours'))->format('H:i:s')
                        );
                })
                ->orWhere('attempted_at', null);
            })
            ->get();
    }

    /**
     * [paginateByUser description]
     * @param  int                  $id [description]
     * @return LengthAwarePaginator     [description]
     */
    public function paginateByUser(int $id) : LengthAwarePaginator
    {
        return $this->dir->where('user_id', $id)->paginate();
    }

    /**
     * [deactivateByBacklink description]
     * @return bool [description]
     */
    public function deactivateByBacklink() : bool
    {
        return $this->dir->update(['status' => 3]);
    }

    /**
     * [activate description]
     * @return bool [description]
     */
    public function activate() : bool
    {
        return $this->dir->update(['status' => 1]);
    }

    /**
     * [nullPrivileged description]
     * @return bool [description]
     */
    public function nullablePrivileged() : bool
    {
        return $this->dir->update([
            'privileged_at' => null,
            'privileged_to' => null
        ]);
    }
}
