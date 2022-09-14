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

namespace N1ebieski\IDir\Repositories\Category;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Category\Category;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use N1ebieski\ICore\Repositories\Category\CategoryRepo as BaseCategoryRepo;

class CategoryRepo extends BaseCategoryRepo
{
    /**
     * Undocumented function
     *
     * @param array $component
     * @return Collection
     */
    public function getRootsByComponent(array $component): Collection
    {
        return $this->category->newQuery()
            ->when($component['count'] === true, function (Builder $query) {
                /** @var Model */
                // @phpstan-ignore-next-line
                $morph = $this->category->morphs()->make();

                $morphs = $this->category->newQuery()
                    ->selectRaw("`categories`.`id`, `{$morph->getTable()}`.`id` as `morph_id`")
                    ->leftJoin('categories_closure', function (JoinClause $query) {
                        return $query->on('categories.id', '=', 'categories_closure.ancestor');
                    })
                    ->leftJoin('categories_models', function (JoinClause $query) {
                        return $query->on('categories_closure.descendant', '=', 'categories_models.category_id');
                    })
                    ->leftJoin($morph->getTable(), function (JoinClause $query) use ($morph) {
                        return $query->on('categories_models.model_id', '=', "{$morph->getTable()}.id")
                            ->where('categories_models.model_type', $morph->getMorphClass())
                            // @phpstan-ignore-next-line
                            ->where("{$morph->getTable()}.status", $morph->status::ACTIVE);
                    })
                    ->groupBy("{$morph->getTable()}.id", 'categories.id');

                return $query->selectRaw('`categories`.*, COUNT(`morphs`.`morph_id`) as `nested_morphs_count`')
                    ->joinSub($morphs->getQuery(), 'morphs', function (JoinClause $query) {
                        return $query->on('categories.id', '=', 'morphs.id');
                    })
                    ->groupBy('categories.id');
            })
            ->poliType()
            ->active()
            ->root()
            ->orderBy('position', 'asc')
            ->get();
    }

    /**
     * Undocumented function
     *
     * @param array $component
     * @return Collection
     */
    public function getWithChildrensByComponent(array $component): Collection
    {
        return $this->category->newQuery()
            ->when($component['category_count'] === true, function (Builder $query) {
                /** @var Model */
                // @phpstan-ignore-next-line
                $morph = $this->category->morphs()->make();

                $morphs = $this->category->newQuery()
                    ->selectRaw("`categories`.`id`, `{$morph->getTable()}`.`id` as `morph_id`")
                    ->leftJoin('categories_closure', function (JoinClause $query) {
                        return $query->on('categories.id', '=', 'categories_closure.ancestor');
                    })
                    ->leftJoin('categories_models', function (JoinClause $query) {
                        return $query->on('categories_closure.descendant', '=', 'categories_models.category_id');
                    })
                    ->leftJoin($morph->getTable(), function (JoinClause $query) use ($morph) {
                        return $query->on('categories_models.model_id', '=', "{$morph->getTable()}.id")
                            ->where('categories_models.model_type', $morph->getMorphClass())
                            // @phpstan-ignore-next-line
                            ->where("{$morph->getTable()}.status", $morph->status::ACTIVE);
                    })
                    ->groupBy("{$morph->getTable()}.id", 'categories.id');

                return $query->selectRaw('`categories`.*, COUNT(`morphs`.`morph_id`) as `nested_morphs_count`')
                    ->joinSub($morphs->getQuery(), 'morphs', function (JoinClause $query) {
                        return $query->on('categories.id', '=', 'morphs.id');
                    })
                    ->groupBy('categories.id');
            })
            ->when(!is_null($component['parent']), function (Builder $query) use ($component) {
                return $query->where('parent_id', $component['parent']);
            }, function (Builder|Category $query) {
                return $query->root();
            })
            ->poliType()
            ->active()
            ->orderBy('position', 'asc')
            ->with(['childrens' => function (HasMany|Builder|Category $query) use ($component) {
                return $query->orderBy('position', 'asc')
                    ->when($component['children_count'] === true, function (Builder $query) {
                        return $query->withCount([
                            'morphs' => function (MorphToMany|Builder $query) {
                                return $query->active();
                            }
                        ]);
                    });
            }])
            ->get()
            ->map(function (Category $category) use ($component) {
                if ($component['children_shuffle'] === true) {
                    $category->childrens = $category->childrens->shuffle();
                }

                if ($component['children_limit'] > 0) {
                    $category->childrens = $category->childrens->take($component['children_limit']);
                }

                return $category;
            });
    }

    /**
     * [paginateDirsByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateDirsByFilter(array $filter): LengthAwarePaginator
    {
        /** @var Dir */
        // @phpstan-ignore-next-line
        $dir = $this->category->morphs()->make();

        return $dir->newQuery()
            ->selectRaw('`dirs`.*, IF(`privileges_ancestor`.`name` IS NULL, 0, 1) as `privilege_ancestor`, IF(`privileges_self`.`name` IS NULL, 0, 1) as `privilege_self`')
            ->leftJoin('groups_privileges AS groups_privileges_ancestor', function (JoinClause $query) {
                return $query->on('dirs.group_id', '=', 'groups_privileges_ancestor.group_id')
                    ->join('privileges AS privileges_ancestor', 'groups_privileges_ancestor.privilege_id', '=', 'privileges_ancestor.id')
                    ->whereIn('privileges_ancestor.name', [
                        'highest position in ancestor categories',
                    ]);
            })
            ->leftJoin('groups_privileges AS groups_privileges_self', function (JoinClause $query) {
                return $query->on('dirs.group_id', '=', 'groups_privileges_self.group_id')
                    ->join('privileges AS privileges_self', 'groups_privileges_self.privilege_id', '=', 'privileges_self.id')
                    ->whereIn('privileges_self.name', [
                        'highest position in their categories'
                    ]);
            })
            ->join('categories_models', function (JoinClause $query) use ($dir) {
                return $query->on('dirs.id', '=', 'categories_models.model_id')
                    ->where('categories_models.model_type', $dir->getMorphClass())
                    ->whereIn('categories_models.category_id', $this->category->descendants->pluck('id')->toArray());
            })
            ->where(function (Builder $query) {
                return $query->where(DB::raw('IF(`privileges_ancestor`.`name` IS NULL, 0, 1)'), 1)
                    ->orWhere(function (Builder $query) {
                        return $query->where(DB::raw('IF(`privileges_ancestor`.`name` IS NULL, 0, 1)'), 0)
                            ->where('categories_models.category_id', $this->category->id);
                    });
            })
            ->active()
            ->filterRegion($filter['region'])
            ->when(is_null($filter['orderby']), function (Builder $query) {
                return $query->orderBy('privilege_self', 'desc')
                    ->orderBy('privilege_ancestor', 'desc')
                    ->latest();
            })
            ->when(!is_null($filter['orderby']), function (Builder|Category $query) use ($filter) {
                return $query->filterOrderBy($filter['orderby']);
            })
            ->groupBy('dirs.id', DB::raw('IF(`privileges_ancestor`.`name` IS NULL, 0, 1)'), DB::raw('IF(`privileges_self`.`name` IS NULL, 0, 1)'))
            ->withAllPublicRels()
            ->filterPaginate($this->config->get('database.paginate'));
    }
}
