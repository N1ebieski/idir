<?php

namespace N1ebieski\IDir\Cache;

use Illuminate\Support\Carbon;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Collect;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;

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
     * Undocumented variable
     *
     * @var Carbon
     */
    protected $carbon;

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
     * Undocumented function
     *
     * @param Dir $dir
     * @param Cache $cache
     * @param Config $config
     * @param Carbon $carbon
     * @param Collect $collect
     */
    public function __construct(
        Dir $dir,
        Cache $cache,
        Config $config,
        Carbon $carbon,
        Collect $collect
    ) {
        $this->dir = $dir;

        $this->cache = $cache;
        $this->config = $config;
        $this->carbon = $carbon;
        $this->collect = $collect;

        $this->minutes = $config->get('cache.minutes');
    }

    /**
     * [getForWebByFilter description]
     * @param  int                  $page [description]
     * @return LengthAwarePaginator|null       [description]
     */
    public function getForWebByFilter(int $page): ?LengthAwarePaginator
    {
        return $this->cache->tags(['dirs'])->get("dir.paginateByFilter.{$page}");
    }

    /**
     * [putForWebByFilter description]
     * @param  LengthAwarePaginator $dirs [description]
     * @param  int                  $page     [description]
     * @return bool                           [description]
     */
    public function putForWebByFilter(LengthAwarePaginator $dirs, int $page): bool
    {
        return $this->cache->tags(['dirs'])
            ->put(
                "dir.paginateByFilter.{$page}",
                $dirs,
                $this->carbon->now()->addMinutes($this->minutes)
            );
    }

    /**
     * [rememberForWebByFilter description]
     * @param  array        $filter       [description]
     * @param  int          $page         [description]
     * @return LengthAwarePaginator       [description]
     */
    public function rememberForWebByFilter(array $filter, int $page): LengthAwarePaginator
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
    public function rememberThumbnailUrl(): string
    {
        return $this->cache->remember(
            "dir.thumbnailUrl.{$this->dir->slug}",
            $this->carbon->now()->addDays($this->config->get('idir.dir.thumbnail.cache.days')),
            function () {
                return $this->config->get('idir.dir.thumbnail.cache.url')
                    . app('crypt.thumbnail')->encryptString($this->dir->url);
            }
        );
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
            $this->carbon->now()->addMinutes($this->minutes),
            function () use ($slug) {
                return $this->dir->makeRepo()->firstBySlug($slug);
            }
        );
    }

    /**
     * [rememberLoadAllPublicRels description]
     * @return Dir [description]
     */
    public function rememberLoadAllPublicRels(): Dir
    {
        return $this->cache->tags(["dir.{$this->dir->slug}"])->remember(
            "dir.{$this->dir->slug}.loadAllPublicRels",
            $this->carbon->now()->addMinutes($this->minutes),
            function () {
                return $this->dir->loadAllPublicRels();
            }
        );
    }

    /**
     * [rememberRelated description]
     * @return Collection [description]
     */
    public function rememberRelated(): Collection
    {
        return $this->cache->tags(["dir.{$this->dir->slug}"])->remember(
            "dir.getRelated.{$this->dir->id}",
            $this->carbon->now()->addMinutes($this->minutes),
            function () {
                return $this->dir->makeRepo()->getRelated();
            }
        );
    }

    /**
     * [rememberLatestForHome description]
     * @return Collection [description]
     */
    public function rememberLatestForHome(): Collection
    {
        return $this->cache->tags(["dirs"])->remember(
            "dir.getLatestForHome",
            $this->carbon->now()->addMinutes($this->minutes),
            function () {
                return $this->dir->makeRepo()->getLatestForHome();
            }
        );
    }

    /**
     * Undocumented function
     *
     * @param array $component
     * @return Collection
     */
    public function rememberAdvertisingPrivilegedByComponent(array $component): Collection
    {
        $json = json_encode($component);

        return $this->cache->tags(["dirs"])->remember(
            "dir.getAdvertisingPrivilegedByComponent.{$json}",
            $this->carbon->now()->addMinutes($this->minutes),
            function () use ($component) {
                return $this->dir->makeRepo()->getAdvertisingPrivilegedByComponent($component);
            }
        );
    }

    /**
     * Undocumented function
     *
     * @param array $component
     * @return Collection
     */
    public function rememberByComponent(array $component): Collection
    {
        $json = json_encode($component);

        return $this->cache->tags(["dirs"])->remember(
            "dir.getByComponent.{$json}",
            $this->carbon->now()->addMinutes($this->minutes),
            function () use ($component) {
                return $this->dir->makeRepo()->getByComponent($component);
            }
        );
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function rememberFriendsPrivileged(): Collection
    {
        return $this->cache->tags(["dirs"])->remember(
            "dir.getFriendsPrivileged",
            $this->carbon->now()->addMinutes($this->minutes),
            function () {
                return $this->dir->makeRepo()->getFriendsPrivileged();
            }
        );
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function rememberCountByStatus(): Collection
    {
        return $this->cache->tags(['dirs'])->remember(
            "dir.countByStatus",
            $this->carbon->now()->addMinutes($this->minutes),
            function () {
                return $this->dir->makeRepo()->countByStatus();
            }
        );
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function rememberLastActivity(): ?string
    {
        return $this->cache->tags(['dirs'])->remember(
            "dir.getlastActivity",
            $this->carbon->now()->addMinutes($this->minutes),
            function () {
                return $this->dir->makeRepo()->getLastActivity();
            }
        );
    }
}
