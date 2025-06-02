<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Repositories\Group;

use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Field\Field;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\IDir\ValueObjects\Group\Slug;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GroupRepo
{
    /**
     * Undocumented function
     *
     * @param Group $group
     * @param Auth $auth
     */
    public function __construct(
        protected Group $group,
        protected Auth $auth
    ) {
        //
    }

    /**
     * [paginate description]
     * @param  array        $filter [description]
     * @return LengthAwarePaginator [description]
     */
    public function paginateByFilter(array $filter): LengthAwarePaginator
    {
        return $this->group->newQuery()
            ->selectRaw("`{$this->group->getTable()}`.*")
            ->when(!is_null($filter['search']), function (Builder|Group $query) use ($filter) {
                return $query->filterSearch($filter['search'])
                    ->when($this->auth->user()?->can('admin.groups.view'), function (Builder $query) {
                        return $query->where(function (Builder $query) {
                            foreach (['id'] as $attr) {
                                $query = $query->when(array_key_exists($attr, $this->group->search), function (Builder $query) use ($attr) {
                                    return $query->where("{$this->group->getTable()}.{$attr}", $this->group->search[$attr]);
                                });
                            }

                            return $query;
                        });
                    });
            })
            ->when(
                is_null($filter['visible']) && !$this->auth->user()?->can('admin.groups.view'),
                function (Builder|Group $query) {
                    return $query->public();
                },
                function (Builder|Group $query) use ($filter) {
                    return $query->filterVisible($filter['visible']);
                }
            )
            ->when(is_null($filter['orderby']), function (Builder|Group $query) use ($filter) {
                $query->filterOrderBySearch($filter['search']);
            })
            ->filterExcept($filter['except'])
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
        return $this->group->siblings()->pluck('position', 'id')->toArray();
    }

    /**
     * [getPublicWithRels description]
     * @return Collection [description]
     */
    public function getPublicWithRels(): Collection
    {
        return $this->group->newQuery()
            ->public()
            ->orderBy('position', 'asc')
            ->with(['privileges', 'prices'])
            ->withCount(['dirs', 'dirsToday'])
            ->get();
    }

    /**
     * [getWithRels description]
     * @return Collection [description]
     */
    public function getWithRels(): Collection
    {
        return $this->group->newQuery()
            ->orderBy('position', 'asc')
            ->with(['privileges', 'prices'])
            ->withCount(['dirs', 'dirsToday'])
            ->get();
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function getExceptDefault(): Collection
    {
        return $this->group->newQuery()
            ->exceptDefault()
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
        return $this->group->newQuery()
            ->where('id', $id)
            ->with(['fields' => function (MorphToMany|Builder|Field $query) {
                return $query->public();
            }])
            ->first();
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function getPublic(): Collection
    {
        return $this->group->newQuery()->public()->orderBy('position', 'asc')->get();
    }

    /**
     * [getWithRole description]
     * @param  int        $id [description]
     * @return Collection     [description]
     */
    public function getWithField(int $id): Collection
    {
        return $this->group->newQuery()
            ->with(['fields' => function (MorphToMany|Builder|Field $query) use ($id) {
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
        return $this->group->newQuery()
            ->where('id', '!=', $this->group->id)
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
        return $this->group->newQuery()
            ->whereDoesntHave('prices')
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
        return $this->group->newQuery()->where('slug', $slug->getValue())->first();
    }
}
