<?php

namespace N1ebieski\IDir\Repositories;

use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Group;
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
     * @param  array        $filter [description]
     * @return LengthAwarePaginator [description]
     */
    public function paginateByFilter(array $filter) : LengthAwarePaginator
    {
        return $this->group->filterSearch($filter['search'])
            ->filterVisible($filter['visible'])
            ->filterOrderBy($filter['orderby'] ?? 'position|asc')
            ->filterPaginate($filter['paginate']);
    }

    /**
     * [getSiblingsAsArray description]
     * @return array [description]
     */
    public function getSiblingsAsArray() : array
    {
        return $this->group->siblings()
            ->get(['id', 'position'])
            ->pluck('position', 'id')
            ->toArray();
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
     * [getPricesByType description]
     * @param  string     $type [description]
     * @return Collection       [description]
     */
    public function getPricesByType(string $type) : Collection
    {
        return $this->group->prices()
            ->where('type', $type)
            ->orderBy('price', 'asc')
            ->get(['id', 'price', 'days']);
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
            ->first();
    }
}
