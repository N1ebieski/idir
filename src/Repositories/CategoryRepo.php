<?php

namespace N1ebieski\IDir\Repositories;

use N1ebieski\IDir\Models\Category\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\ICore\Repositories\CategoryRepo as BaseCategoryRepo;
use Illuminate\Support\Facades\DB;

/**
 * [CommentRepo description]
 */
class CategoryRepo extends BaseCategoryRepo
{
    /**
     * [__construct description]
     * @param Category   $category   [description]
     * @param Config     $config     [description]
     */
    public function __construct(Category $category, Config $config)
    {
        parent::__construct($category, $config);
    }

    /**
     * [getRootsWithNestedMorphsCount description]
     * @return Collection [description]
     */
    public function getRootsWithNestedMorphsCount() : Collection
    {
        $morph = $this->category->morphs()->make();

        return $this->category->selectRaw('`categories`.*, COUNT(`categories_models`.`model_id`) AS `nested_morphs_count`')
            ->leftJoin('categories_closure', function ($query) {
                $query->on('categories.id', '=', 'categories_closure.ancestor');
            })
            ->leftJoin('categories_models', function ($query) {
                $query->on('categories_closure.descendant', '=', 'categories_models.category_id');
            })
            ->join($morph->getTable(), function ($query) use ($morph) {
                $query->on('categories_models.model_id', '=', "{$morph->getTable()}.id")
                    ->where('categories_models.model_type', $morph->getMorphClass())
                    ->where("{$morph->getTable()}.status", 1);
            })
            ->groupBy('categories.id')
            ->poliType()
            ->active()
            ->root()
            ->orderBy('position', 'asc')
            ->get();
    }

    /**
     * [paginateDirsByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateDirsByFilter(array $filter) : LengthAwarePaginator
    {
        $dir = $this->category->morphs()->make();

        // Rezygnuje z eloquentowych whereHas (whereExists) na rzecz joinów, bo rekordów jest dużo i siada wydajność
        return $dir->selectRaw('`dirs`.*, IF(`privileges`.`name` IS NULL, 0, 1) as `privilege`')
            ->withAllPublicRels()
            ->leftJoin('groups_privileges', function ($query) {
                $query->on('dirs.group_id', '=', 'groups_privileges.group_id')
                    ->join('privileges', 'groups_privileges.privilege_id', '=', 'privileges.id')
                    ->whereIn('privileges.name', [
                        'highest position in ancestor categories',
                        'highest position in their categories'
                    ]);
            })
            ->join('categories_models', function ($query) use ($dir) {
                $query->on('dirs.id', '=', 'categories_models.model_id')
                    ->where('categories_models.model_type', $dir->getMorphClass())
                    ->whereIn('categories_models.category_id', $this->category->descendants->pluck('id')->toArray());
            })
            ->where(function ($query) {
                $query->where(DB::raw('IF(`privileges`.`name` IS NULL, 0, 1)'), 1)
                    ->orWhere(function ($query) {
                        $query->where(DB::raw('IF(`privileges`.`name` IS NULL, 0, 1)'), 0)
                            ->where('categories_models.category_id', $this->category->id);
                    });
            })
            ->active()
            ->filterRegion($filter['region'])
            ->when($filter['orderby'] === null, function ($query) {
                $query->orderBy('privilege', 'desc')->latest();
            })
            ->when($filter['orderby'] !== null, function ($query) use ($filter) {
                $query->filterOrderBy($filter['orderby']);
            })
            ->groupBy('dirs.id', DB::raw('IF(`privileges`.`name` IS NULL, 0, 1)'))
            ->filterPaginate($this->paginate);
    }
}
