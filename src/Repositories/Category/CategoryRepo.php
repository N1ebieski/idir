<?php

namespace N1ebieski\IDir\Repositories\Category;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
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
        return $this->category
            ->when($component['count'] === true, function ($query) {
                $morph = $this->category->morphs()->make();

                $morphs = $this->category
                    ->selectRaw("`categories`.`id`, `{$morph->getTable()}`.`id` as `morph_id`")
                    ->leftJoin('categories_closure', function ($query) {
                        $query->on('categories.id', '=', 'categories_closure.ancestor');
                    })
                    ->leftJoin('categories_models', function ($query) {
                        $query->on('categories_closure.descendant', '=', 'categories_models.category_id');
                    })
                    ->leftJoin($morph->getTable(), function ($query) use ($morph) {
                        $query->on('categories_models.model_id', '=', "{$morph->getTable()}.id")
                            ->where('categories_models.model_type', $morph->getMorphClass())
                            ->where("{$morph->getTable()}.status", $morph->status::ACTIVE);
                    })
                    ->groupBy("{$morph->getTable()}.id", 'categories.id');

                $query->selectRaw('`categories`.*, COUNT(`morphs`.`morph_id`) as `nested_morphs_count`')
                    ->joinSub($morphs, 'morphs', function ($query) {
                        $query->on('categories.id', '=', 'morphs.id');
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
        return $this->category->when($component['category_count'] === true, function ($query) {
                $morph = $this->category->morphs()->make();

                $morphs = $this->category
                    ->selectRaw("`categories`.`id`, `{$morph->getTable()}`.`id` as `morph_id`")
                    ->leftJoin('categories_closure', function ($query) {
                        $query->on('categories.id', '=', 'categories_closure.ancestor');
                    })
                    ->leftJoin('categories_models', function ($query) {
                        $query->on('categories_closure.descendant', '=', 'categories_models.category_id');
                    })
                    ->leftJoin($morph->getTable(), function ($query) use ($morph) {
                        $query->on('categories_models.model_id', '=', "{$morph->getTable()}.id")
                            ->where('categories_models.model_type', $morph->getMorphClass())
                            ->where("{$morph->getTable()}.status", $morph->status::ACTIVE);
                    })
                    ->groupBy("{$morph->getTable()}.id", 'categories.id');

                $query->selectRaw('`categories`.*, COUNT(`morphs`.`morph_id`) as `nested_morphs_count`')
                    ->joinSub($morphs, 'morphs', function ($query) {
                        $query->on('categories.id', '=', 'morphs.id');
                    })
                    ->groupBy('categories.id');
        })
            ->when($component['parent'] !== null, function ($query) use ($component) {
                $query->where('parent_id', $component['parent']);
            }, function ($query) {
                $query->root();
            })
            ->poliType()
            ->active()
            ->orderBy('position', 'asc')
            ->with(['childrens' => function ($query) use ($component) {
                $query->orderBy('position', 'asc')
                    ->when($component['children_count'] === true, function ($query) {
                        $query->withCount([
                            'morphs' => function ($query) {
                                $query->active();
                            }
                        ]);
                    });
            }])
            ->get()
            ->map(function ($item) use ($component) {
                if ($component['children_shuffle'] === true) {
                    $item->childrens = $item->childrens->shuffle();
                }

                if ($component['children_limit'] > 0) {
                    $item->childrens = $item->childrens->take($component['children_limit']);
                }

                return $item;
            });
    }

    /**
     * [paginateDirsByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateDirsByFilter(array $filter): LengthAwarePaginator
    {
        $dir = $this->category->morphs()->make();

        return $dir->selectRaw('`dirs`.*, IF(`privileges_ancestor`.`name` IS NULL, 0, 1) as `privilege_ancestor`, IF(`privileges_self`.`name` IS NULL, 0, 1) as `privilege_self`')
            ->withAllPublicRels()
            ->leftJoin('groups_privileges AS groups_privileges_ancestor', function ($query) {
                $query->on('dirs.group_id', '=', 'groups_privileges_ancestor.group_id')
                    ->join('privileges AS privileges_ancestor', 'groups_privileges_ancestor.privilege_id', '=', 'privileges_ancestor.id')
                    ->whereIn('privileges_ancestor.name', [
                        'highest position in ancestor categories',
                    ]);
            })
            ->leftJoin('groups_privileges AS groups_privileges_self', function ($query) {
                $query->on('dirs.group_id', '=', 'groups_privileges_self.group_id')
                    ->join('privileges AS privileges_self', 'groups_privileges_self.privilege_id', '=', 'privileges_self.id')
                    ->whereIn('privileges_self.name', [
                        'highest position in their categories'
                    ]);
            })
            ->join('categories_models', function ($query) use ($dir) {
                $query->on('dirs.id', '=', 'categories_models.model_id')
                    ->where('categories_models.model_type', $dir->getMorphClass())
                    ->whereIn('categories_models.category_id', $this->category->descendants->pluck('id')->toArray());
            })
            ->where(function ($query) {
                $query->where(DB::raw('IF(`privileges_ancestor`.`name` IS NULL, 0, 1)'), 1)
                    ->orWhere(function ($query) {
                        $query->where(DB::raw('IF(`privileges_ancestor`.`name` IS NULL, 0, 1)'), 0)
                            ->where('categories_models.category_id', $this->category->id);
                    });
            })
            ->active()
            ->filterRegion($filter['region'])
            ->when($filter['orderby'] === null, function ($query) {
                $query->orderBy('privilege_self', 'desc')
                    ->orderBy('privilege_ancestor', 'desc')
                    ->latest();
            })
            ->when($filter['orderby'] !== null, function ($query) use ($filter) {
                $query->filterOrderBy($filter['orderby']);
            })
            ->groupBy('dirs.id', DB::raw('IF(`privileges_ancestor`.`name` IS NULL, 0, 1)'), DB::raw('IF(`privileges_self`.`name` IS NULL, 0, 1)'))
            ->filterPaginate($this->config->get('database.paginate'));
    }
}