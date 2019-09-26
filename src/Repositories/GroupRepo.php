<?php

namespace N1ebieski\IDir\Repositories;

use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Group\Group;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Config\Repository as Config;

/**
 * [CommentRepo description]
 */
class GroupRepo
{
    /**
     * [private description]
     * @var Group
     */
    private $group;

    /**
     * Config
     * @var int
     */
    private $paginate;

    /**
     * [__construct description]
     * @param Group $group [description]
     * @param Config   $config   [description]
     */
    public function __construct(Group $group, Config $config)
    {
        $this->group = $group;
        $this->paginate = $config->get('database.paginate');
    }

    /**
     * [paginate description]
     * @return LengthAwarePaginator [description]
     */
    public function paginate() : LengthAwarePaginator
    {
        return $this->group->orderBy('position', 'asc')
            ->poliType()
            ->paginate($this->paginate);
    }

    /**
     * [getSiblingsAsArray description]
     * @return array [description]
     */
    public function getSiblingsAsArray() : array
    {
        return $this->group->siblings()->get(['id', 'position'])
            ->pluck('position', 'id')->toArray();
    }

    /**
     * [getAvailable description]
     * @return Collection [description]
     */
    public function getPublicWithPrivileges() : Collection
    {
        return $this->group->public()
            ->with('privileges')
            ->orderBy('position', 'asc')
            ->get();
    }

    /**
     * [firstPublicById description]
     * @param  int    $id [description]
     * @return Group|null     [description]
     */
    public function firstPublicById(int $id) : ?Group
    {
        return $this->group->where('id', $id)
            ->public()
            ->poliType()
            ->first();
    }
}
