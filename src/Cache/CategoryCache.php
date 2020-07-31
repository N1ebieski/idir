<?php

namespace N1ebieski\IDir\Cache;

use N1ebieski\IDir\Models\Category\Category;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\Cache\CategoryCache as BaseCategoryCache;
use Illuminate\Support\Collection as Collect;
use Illuminate\Support\Carbon;

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
     * [private description]
     * @var Collect
     */
    protected $collect;

    /**
     * [__construct description]
     * @param Category     $category     [description]
     * @param Cache        $cache        [description]
     * @param Config       $config       [description]
     * @param Collect      $collect      [description]
     */
    public function __construct(
        Category $category,
        Cache $cache,
        Config $config,
        Carbon $carbon,
        Collect $collect
    ) {
        parent::__construct($category, $cache, $config, $carbon);

        $this->collect = $collect;
    }

    /**
     * Undocumented function
     *
     * @param array $component
     * @return Collection
     */
    public function rememberRootsByComponent(array $component) : Collection
    {
        return $this->cache->tags(['categories'])->remember(
            "category.{$this->category->poli}.getRootsByComponent",
            $this->carbon->now()->addMinutes($this->minutes),
            function () use ($component) {
                return $this->category->makeRepo()->getRootsByComponent($component);
            }
        );
    }

    /**
     * Undocumented function
     *
     * @param array $component
     * @return Collection
     */
    public function rememberRootsWithChildrensByComponent(array $component) : Collection
    {
        return $this->cache->tags(['categories'])->remember(
            "category.{$this->category->poli}.getRootsWithChildrensByComponent",
            $this->carbon->now()->addMinutes($this->minutes),
            function () use ($component) {
                return $this->category->makeRepo()->getRootsWithChildrensByComponent($component);
            }
        );
    }

    /**
     * [getDirsByFilter description]
     * @param  array                $filter    [description]
     * @param  int                  $page [description]
     * @return LengthAwarePaginator|null       [description]
     */
    public function getDirsByFilter(array $filter, int $page) : ?LengthAwarePaginator
    {
        return $this->cache->tags(['dirs'])
            ->get("category.{$this->category->id}.paginateDirsByFilter.{$filter['region']}.{$page}");
    }

    /**
     * [putDirsByFilter description]
     * @param  LengthAwarePaginator $dirs [description]
     * @param  array                $filter   [description]
     * @param  int                  $page     [description]
     * @return bool                           [description]
     */
    public function putDirsByFilter(LengthAwarePaginator $dirs, array $filter, int $page) : bool
    {
        return $this->cache->tags(['dirs'])
            ->put(
                "category.{$this->category->id}.paginateDirsByFilter.{$filter['region']}.{$page}",
                $dirs,
                $this->carbon->now()->addMinutes($this->minutes)
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
        if ($this->collect->make($filter)->except(['region'])->isNullItems()) {
            $dirs = $this->getDirsByFilter($filter, $page);
        }

        if (!isset($dirs) || !$dirs) {
            $dirs = $this->category->makeRepo()->paginateDirsByFilter($filter);

            if ($this->collect->make($filter)->except(['region'])->isNullItems()) {
                $this->putDirsByFilter($dirs, $filter, $page);
            }
        }

        return $dirs;
    }
    
    /**
     * [rememberLoadNestedWithMorphsCount description]
     * @param  array    $filter    [description]
     * @return Category [description]
     */
    public function rememberLoadNestedWithMorphsCountByFilter(array $filter) : Category
    {
        return $this->cache->tags(["category.{$this->category->slug}"])->remember(
            "category.{$this->category->slug}.loadNestedWithMorphsCountByFilter.{$filter['region']}",
            $this->carbon->now()->addMinutes($this->minutes),
            function () use ($filter) {
                return $this->category->loadNestedWithMorphsCountByFilter($filter);
            }
        );
    }
}
