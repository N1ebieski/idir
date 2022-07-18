<?php

namespace N1ebieski\IDir\Repositories\Group;

use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\ValueObjects\Group\Slug;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class GroupRepo
{
    /**
     * [private description]
     * @var Group
     */
    protected $group;

    /**
     * Undocumented variable
     *
     * @var Auth
     */
    protected $auth;

    /**
     * Undocumented function
     *
     * @param Group $group
     * @param Auth $auth
     */
    public function __construct(Group $group, Auth $auth)
    {
        $this->group = $group;

        $this->auth = $auth;
    }

    /**
     * [paginate description]
     * @param  array        $filter [description]
     * @return LengthAwarePaginator [description]
     */
    public function paginateByFilter(array $filter): LengthAwarePaginator
    {
        return $this->group->selectRaw("`{$this->group->getTable()}`.*")
            ->filterSearch($filter['search'])
            ->filterExcept($filter['except'])
            ->when(
                $filter['visible'] === null && !optional($this->auth->user())->can('admin.groups.view'),
                function ($query) {
                    $query->public();
                },
                function ($query) use ($filter) {
                    $query->filterVisible($filter['visible']);
                }
            )
            ->when($filter['orderby'] === null, function ($query) use ($filter) {
                $query->filterOrderBySearch($filter['search']);
            })
            ->filterOrderBy($filter['orderby'] ?? 'position|asc')
            ->withCount('prices')
            ->with(['prices', 'privileges', 'fields'])
            ->filterPaginate($filter['paginate']);
    }

    /**
     * [getSiblingsAsArray description]
     * @return array [description]
     */
    public function getSiblingsAsArray(): array
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
    public function getPublicWithRels(): Collection
    {
        return $this->group->public()
            ->with(['privileges', 'prices'])
            ->withCount(['dirs', 'dirsToday'])
            ->orderBy('position', 'asc')
            ->get();
    }

    /**
     * [getWithRels description]
     * @return Collection [description]
     */
    public function getWithRels(): Collection
    {
        return $this->group
            ->with(['privileges', 'prices'])
            ->withCount(['dirs', 'dirsToday'])
            ->orderBy('position', 'asc')
            ->get();
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function getExceptDefault(): Collection
    {
        return $this->group->exceptDefault()
            ->orderBy('position', 'asc')
            ->get();
    }

    /**
     * [getPricesByType description]
     * @param  string     $type [description]
     * @return Collection       [description]
     */
    public function getPricesByType(string $type): Collection
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
    public function firstWithRelsById(int $id): ?Group
    {
        return $this->group->where('id', $id)
            ->with(['fields' => function ($query) {
                return $query->public();
            }])
            // ->withCount(['dirs', 'dirsToday'])
            ->first();
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function getPublic(): Collection
    {
        return $this->group->public()
            ->orderBy('position', 'asc')->get();
    }

    /**
     * [getWithRole description]
     * @param  int        $id [description]
     * @return Collection     [description]
     */
    public function getWithField(int $id): Collection
    {
        return $this->group->with([
            'fields' => function ($query) use ($id) {
                $query->where('field_id', $id);
            }])
            ->orderBy('position', 'asc')
            ->get();
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function getExceptSelf(): Collection
    {
        return $this->group->where('id', '!=', $this->group->id)
            ->orderBy('position', 'asc')
            ->get();
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function getDoesntHavePricesExceptSelf(): Collection
    {
        return $this->group->whereDoesntHave('prices')
            ->where('id', '!=', $this->group->id)
            ->orderBy('position', 'asc')
            ->get();
    }

    /**
     * Undocumented function
     *
     * @param Slug $slug
     * @return Group|null
     */
    public function firstBySlug(Slug $slug): ?Group
    {
        return $this->group->where('slug', $slug->getValue())->first();
    }
}
