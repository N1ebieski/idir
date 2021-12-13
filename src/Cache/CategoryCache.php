<?php

namespace N1ebieski\IDir\Cache;

use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Category\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use N1ebieski\ICore\Cache\CategoryCache as BaseCategoryCache;

/**
 * @property Category $category
 */
class CategoryCache extends BaseCategoryCache
{
    /**
     * Undocumented function
     *
     * @param array $component
     * @return Collection
     */
    public function rememberRootsByComponent(array $component): Collection
    {
        $json = json_encode($component);

        return $this->cache->tags(['categories'])->remember(
            "category.{$this->category->poli}.getRootsByComponent.{$json}",
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
    public function rememberWithChildrensByComponent(array $component): Collection
    {
        $json = json_encode($component);

        return $this->cache->tags(['categories'])->remember(
            "category.{$this->category->poli}.getWithChildrensByComponent.{$json}",
            $this->carbon->now()->addMinutes($this->minutes),
            function () use ($component) {
                return $this->category->makeRepo()->getWithChildrensByComponent($component);
            }
        );
    }

    /**
     * [getDirsByFilter description]
     * @param  array                $filter    [description]
     * @param  int                  $page [description]
     * @return LengthAwarePaginator|null       [description]
     */
    public function getDirsByFilter(array $filter, int $page): ?LengthAwarePaginator
    {
        $regionId = optional($filter['region'])->id;

        return $this->cache->tags(['dirs'])
            ->get("category.{$this->category->id}.paginateDirsByFilter.{$regionId}.{$page}");
    }

    /**
     * [putDirsByFilter description]
     * @param  LengthAwarePaginator $dirs [description]
     * @param  array                $filter   [description]
     * @param  int                  $page     [description]
     * @return bool                           [description]
     */
    public function putDirsByFilter(LengthAwarePaginator $dirs, array $filter, int $page): bool
    {
        $regionId = optional($filter['region'])->id;

        return $this->cache->tags(['dirs'])
            ->put(
                "category.{$this->category->id}.paginateDirsByFilter.{$regionId}.{$page}",
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
    public function rememberDirsByFilter(array $filter, int $page): LengthAwarePaginator
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
    public function rememberLoadNestedWithMorphsCountByFilter(array $filter): Category
    {
        $regionId = optional($filter['region'])->id;

        return $this->cache->tags(["category.{$this->category->slug}"])->remember(
            "category.{$this->category->slug}.loadNestedWithMorphsCountByFilter.{$regionId}",
            $this->carbon->now()->addMinutes($this->minutes),
            function () use ($filter) {
                return $this->category->loadNestedWithMorphsCountByFilter($filter);
            }
        );
    }
}
