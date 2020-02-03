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
        return $this->category
            ->addSelect(['nested_morphs_count' => function($query) {
                $morph = $this->category->morphs()->make();

                return $query->selectRaw('COUNT(*)')
                    ->from($morph->getTable())
                    ->join('categories_models', "{$morph->getTable()}.id", '=', 'categories_models.model_id')
                    ->where('categories_models.model_type', get_class($morph))
                    ->whereIn('categories_models.category_id', function($query) {
                        return $query->from('categories_closure')
                            ->select('descendant')
                            ->whereColumn('ancestor', 'categories.id');
                    })
                    ->where("{$morph->getTable()}.status", 1);
            }])
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
        $relations = [
            'group',
            'group.fields' => function($query) {
                return $query->public();
            },
            'group.privileges',                
            'fields',
            'regions',
            'categories', 
            'tags', 
            'user'
        ];

        $sqlPrivilege = DB::table('privileges')
            ->join('groups_privileges', 'privileges.id', '=', 'groups_privileges.privilege_id')
            ->whereColumn('dirs.group_id', 'groups_privileges.group_id')
            ->whereRaw('`privileges`.`name` = "highest position in their categories"')
            ->toSql();

        $dirs = $this->category->morphs()
            ->selectRaw('`dirs`.*, CASE WHEN EXISTS (' . $sqlPrivilege . ') THEN TRUE ELSE FALSE END AS `privilege`')
            ->with($relations)
            ->withSumRating()
            ->active()
            ->filterRegion($filter['region']);

        return $this->category->morphs()
            ->make()
            ->selectRaw('`dirs`.*, TRUE as `privilege`')
            ->with($relations)
            ->withSumRating()
            ->whereHas('group', function($query) {
                $query->whereHas('privileges', function($query) {
                    $query->where('name', 'highest position in ancestor categories');
                });
            })
            ->whereHas('categories', function($query) {
                $query->whereIn('id', $this->category->descendants->pluck('id')->toArray());
            })
            ->active()
            ->union($dirs)
            ->filterRegion($filter['region'])
            ->when($filter['orderby'] === null, function($query) {
                $query->orderBy('privilege', 'desc')->latest();
            })            
            ->when($filter['orderby'] !== null, function($query) use ($filter) {
                $query->filterOrderBy($filter['orderby']);
            })
            ->filterPaginate($this->paginate);
    }    
}
