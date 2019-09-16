<?php

namespace N1ebieski\IDir\Repositories;

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
}
