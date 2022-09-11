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

namespace N1ebieski\IDir\Cache\Category;

use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Category\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use N1ebieski\ICore\Cache\Category\CategoryCache as BaseCategoryCache;

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
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
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
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () use ($component) {
                return $this->category->makeRepo()->getWithChildrensByComponent($component);
            }
        );
    }

    /**
     * [getDirsByFilter description]
     * @param  array                $filter    [description]
     * @return LengthAwarePaginator|null       [description]
     */
    public function getDirsByFilter(array $filter): ?LengthAwarePaginator
    {
        $regionId = optional($filter['region'])->id;

        return $this->cache->tags(['dirs'])
            ->get("category.{$this->category->id}.paginateDirsByFilter.{$regionId}.{$this->request->input('page')}");
    }

    /**
     * [putDirsByFilter description]
     * @param  LengthAwarePaginator $dirs [description]
     * @param  array                $filter   [description]
     * @return bool                           [description]
     */
    public function putDirsByFilter(LengthAwarePaginator $dirs, array $filter): bool
    {
        $regionId = optional($filter['region'])->id;

        return $this->cache->tags(['dirs'])
            ->put(
                "category.{$this->category->id}.paginateDirsByFilter.{$regionId}.{$this->request->input('page')}",
                $dirs,
                $this->carbon->now()->addMinutes($this->config->get('cache.minutes'))
            );
    }

    /**
     * [rememberDirsByFilter description]
     * @param  array        $filter       [description]
     * @return LengthAwarePaginator       [description]
     */
    public function rememberDirsByFilter(array $filter): LengthAwarePaginator
    {
        if ($this->collect->make($filter)->except(['region'])->isNullItems()) {
            $dirs = $this->getDirsByFilter($filter);
        }

        if (!isset($dirs)) {
            $dirs = $this->category->makeRepo()->paginateDirsByFilter($filter);

            if ($this->collect->make($filter)->except(['region'])->isNullItems()) {
                $this->putDirsByFilter($dirs, $filter);
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
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () use ($filter) {
                return $this->category->loadNestedWithMorphsCountByFilter($filter);
            }
        );
    }
}
