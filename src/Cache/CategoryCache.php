<?php

namespace N1ebieski\IDir\Cache;

use N1ebieski\IDir\Models\Category\Category;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\Cache\CategoryCache as BaseCategoryCache;

/**
 * [CategoryCache description]
 */
class CategoryCache extends BaseCategoryCache
{
    /**
     * Link model
     * @var Category
     */
    protected $category;

    /**
     * [__construct description]
     * @param Category     $category     [description]
     * @param Cache        $cache        [description]
     * @param Config       $config       [description]
     */
    public function __construct(Category $category, Cache $cache, Config $config)
    {
        parent::__construct($category, $cache, $config);
    }

    /**
     * [rememberRootsWithNestedMorphsCount description]
     * @return Collection [description]
     */
    public function rememberRootsWithNestedMorphsCount() : Collection
    {
        return $this->cache->tags(['categories'])->remember(
            "category.{$this->category->poli}.getRootsWithNestedMorphsCount",
            now()->addMinutes($this->minutes),
            function() {
                return $this->category->makeRepo()->getRootsWithNestedMorphsCount();
            }
        );
    }

    /**
     * [rememberDirsByFilter description]
     * @param  array        $filter       [description]
     * @param  int          $page         [description]
     * @return LengthAwarePaginator       [description]
     */
    public function rememberDirsByFilter(array $filter, int $page) : LengthAwarePaginator
    {
        return $this->cache->tags(['dirs'])->remember(
            "category.{$this->category->id}.paginateDirsByFilter.{$filter['orderby']}.{$page}",
            now()->addMinutes($this->minutes),
            function () use ($filter) {
                return $this->category->makeRepo()->paginateDirsByFilter($filter);
            }
        );
    }    
}
