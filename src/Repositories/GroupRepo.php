<?php

namespace N1ebieski\IDir\Repositories;

use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Group;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Facades\DB;

/**
 * [CommentRepo description]
 */
class GroupRepo
{
    /**
     * [private description]
     * @var Group
     */
    protected $group;

    /**
     * Config
     * @var int
     */
    protected $paginate;

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
            ->filterExcept($filter['except'])
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
     * [getPublicWithRels description]
     * @return Collection [description]
     */
    public function getPublicWithRels() : Collection
    {
        return $this->group->public()
            ->with(['privileges', 'prices'])
            ->withCount(['dirs', 'dirs_today'])
            ->orderBy('position', 'asc')
            ->get();
    }

    /**
     * [getWithRels description]
     * @return Collection [description]
     */
    public function getWithRels() : Collection
    {
        return $this->group
            ->with(['privileges', 'prices'])
            ->withCount(['dirs', 'dirs_today'])
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
            ->get();
    }

    /**
     * [firstWithRelsById description]
     * @param  int    $id [description]
     * @return Group|null     [description]
     */
    public function firstWithRelsById(int $id) : ?Group
    {
        return $this->group->where('id', $id)
            ->with(['fields' => function($query) {
                return $query->public();
            }])
            // ->withCount(['dirs', 'dirs_today'])
            ->first();
    }

    /**
     * [get description]
     * @return Collection [description]
     */
    public function all() : Collection
    {
        return $this->group->orderBy('position', 'asc')->get();
    }

    /**
     * [getWithRole description]
     * @param  int        $id [description]
     * @return Collection     [description]
     */
    public function getWithField(int $id) : Collection
    {
        return $this->group->with(['fields' => function($query) use ($id) {
                $query->where('field_id', $id);
            }])
            ->orderBy('position', 'asc')
            ->get();
    }
}
