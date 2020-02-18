<?php

namespace N1ebieski\IDir\Cache;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Support\Collection as Collect;

/**
 * [DirCache description]
 */
class DirCache
{
    /**
     * Dir model
     * @var Dir
     */
    protected $dir;

    /**
     * Cache driver
     * @var Cache
     */
    protected $cache;

    /**
     * Configuration
     * @var int
     */
    protected $minutes;

    /**
     * [private description]
     * @var Collect
     */
    protected $collect;

    /**
     * [__construct description]
     * @param Dir   $dir   [description]
     * @param Cache  $cache  [description]
     * @param Config $config [description]
     * @param Collect        $collect        [description]
     */
    public function __construct(Dir $dir, Cache $cache, Config $config, Collect $collect)
    {
        $this->dir = $dir;
        $this->cache = $cache;
        $this->config = $config;
        $this->collect = $collect;

        $this->minutes = $config->get('cache.minutes');
    }

    /**
     * [getForWebByFilter description]
     * @param  int                  $page [description]
     * @return LengthAwarePaginator|null       [description]
     */
    public function getForWebByFilter(int $page) : ?LengthAwarePaginator
    {
        return $this->cache->tags(['dirs'])->get("dir.paginateByFilter.{$page}");
    }

    /**
     * [putForWebByFilter description]
     * @param  LengthAwarePaginator $dirs [description]
     * @param  int                  $page     [description]
     * @return bool                           [description]
     */
    public function putForWebByFilter(LengthAwarePaginator $dirs, int $page) : bool
    {
        return $this->cache->tags(['dirs'])
            ->put(
                "dir.paginateByFilter.{$page}",
                $dirs,
                now()->addMinutes($this->minutes)
            );
    }

    /**
     * [rememberForWebByFilter description]
     * @param  array        $filter       [description]
     * @param  int          $page         [description]
     * @return LengthAwarePaginator       [description]
     */
    public function rememberForWebByFilter(array $filter, int $page) : LengthAwarePaginator
    {
        if ($this->collect->make($filter)->isNullItems()) {
            $dirs = $this->getForWebByFilter($page);
        }

        if (!isset($dirs) || !$dirs) {
            $dirs = $this->dir->makeRepo()->paginateForWebByFilter($filter);

            if ($this->collect->make($filter)->isNullItems()) {
                $this->putForWebByFilter($dirs, $page);
            }
        }

        return $dirs;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function rememberThumbnailUrl() : string
    {
        return $this->cache->remember(
            "dir.thumbnailUrl.{$this->dir->slug}",
            now()->addDays($this->config->get('idir.dir.thumbnail.cache.days')),
            function () {
                return $this->config->get('idir.dir.thumbnail.cache.url')
                    .app('crypt.thumbnail')->encryptString($this->dir->url);
            }
        );
    }

    /**
     * Undocumented function
     *
     * @param Tag $tag
     * @param integer $page
     * @return LengthAwarePaginator|null
     */
    public function getByTagAndFilter(Tag $tag, int $page) : ?LengthAwarePaginator
    {
        return $this->cache->tags(['dirs'])->get("dir.paginateByTagAndFilter.{$tag->normalized}.{$page}");
    }

    /**
     * Undocumented function
     *
     * @param LengthAwarePaginator $dirs
     * @param Tag $tag
     * @param integer $page
     * @return boolean
     */
    public function putByTagAndFilter(LengthAwarePaginator $dirs, Tag $tag, int $page) : bool
    {
        return $this->cache->tags(['dirs'])
            ->put(
                "dir.paginateByTagAndFilter.{$tag->normalized}.{$page}",
                $dirs,
                now()->addMinutes($this->minutes)
            );
    }

    /**
     * [rememberByTagAndFilter description]
     * @param  Tag                  $tag  [description]
     * @param  array                $filter  [description]
     * @param  int                  $page [description]
     * @return LengthAwarePaginator       [description]
     */
    public function rememberByTagAndFilter(Tag $tag, array $filter, int $page) : LengthAwarePaginator
    {
        if ($this->collect->make($filter)->isNullItems()) {
            $dirs = $this->getByTagAndFilter($tag, $page);
        }

        if (!isset($dirs) || !$dirs) {
            $dirs = $this->dir->makeRepo()->paginateByTagAndFilter($tag->name, $filter);

            if ($this->collect->make($filter)->isNullItems()) {
                $this->putByTagAndFilter($dirs, $tag, $page);
            }
        }

        return $dirs;
    }
    
    /**
     * Cache route binding of Dir
     * @param  string $slug [description]
     * @return Region|null       [description]
     */
    public function rememberBySlug(string $slug)
    {
        return $this->cache->tags(["dir.{$slug}"])->remember(
            "dir.firstBySlug.{$slug}",
            now()->addMinutes($this->minutes),
            function () use ($slug) {
                return $this->dir->makeRepo()->firstBySlug($slug);
            }
        );
    }
    
    /**
     * [rememberLoadAllPublicRels description]
     * @return Dir [description]
     */
    public function rememberLoadAllPublicRels() : Dir
    {
        return $this->cache->tags(["dir.{$this->dir->slug}"])->remember(
            "dir.{$this->dir->slug}.loadAllPublicRels",
            now()->addMinutes($this->minutes),
            function () {
                return $this->dir->loadAllPublicRels();
            }
        );
    }
    
    /**
     * [rememberRelated description]
     * @return Collection [description]
     */
    public function rememberRelated() : Collection
    {
        return $this->cache->tags(["dir.{$this->dir->slug}"])->remember(
            "dir.getRelated.{$this->dir->id}",
            now()->addMinutes($this->minutes),
            function () {
                return $this->dir->makeRepo()->getRelated();
            }
        );
    }

    /**
     * [rememberLatest description]
     * @return Collection [description]
     */
    public function rememberLatest() : Collection
    {
        return $this->cache->tags(["dirs"])->remember(
            "dir.getLatest",
            now()->addMinutes($this->minutes),
            function () {
                return $this->dir->makeRepo()->getLatest();
            }
        );
    }

    /**
     * Undocumented function
     *
     * @param array $component
     * @return Collection
     */
    public function rememberAdvertisingPrivilegedByComponent(array $component) : Collection
    {
        return $this->cache->tags(["dirs"])->remember(
            "dir.getAdvertisingPrivilegedByComponent",
            now()->addMinutes($this->minutes),
            function () use ($component) {
                return $this->dir->makeRepo()->getAdvertisingPrivilegedByComponent($component);
            }
        );
    }
}
