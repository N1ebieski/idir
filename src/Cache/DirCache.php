<?php

namespace N1ebieski\IDir\Cache;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

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
     * [__construct description]
     * @param Dir   $dir   [description]
     * @param Cache  $cache  [description]
     * @param Config $config [description]
     */
    public function __construct(Dir $dir, Cache $cache, Config $config)
    {
        $this->dir = $dir;
        $this->cache = $cache;
        $this->config = $config;

        $this->minutes = $config->get('cache.minutes');
    }

    /**
     * [rememberForWebByFilter description]
     * @param  array        $filter       [description]
     * @param  int          $page         [description]
     * @return LengthAwarePaginator       [description]
     */
    public function rememberForWebByFilter(array $filter, int $page) : LengthAwarePaginator
    {
        return $this->cache->tags(['dirs'])->remember(
            "dir.paginateByFilter.{$filter['orderby']}.{$page}",
            now()->addMinutes($this->minutes),
            function () use ($filter) {
                return $this->dir->makeRepo()->paginateForWebByFilter($filter);
            }
        );
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
            function() {
                return $this->config->get('idir.dir.thumbnail.cache.url')
                    .app('crypt.thumbnail')->encryptString($this->dir->url);
            }
        );
    }     
}
