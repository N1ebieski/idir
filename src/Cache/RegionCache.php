<?php

namespace N1ebieski\IDir\Cache;

use N1ebieski\IDir\Models\Region\Region;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Support\Collection as Collect;

/**
 * [RegionCache description]
 */
class RegionCache
{
    /**
     * Region model
     * @var Region
     */
    protected $region;

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
     * @param Region   $region   [description]
     * @param Cache  $cache  [description]
     * @param Config $config [description]
     */
    public function __construct(Region $region, Cache $cache, Config $config)
    {
        $this->region = $region;
        $this->cache = $cache;
        $this->config = $config;      

        $this->minutes = $config->get('cache.minutes');
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function rememberAll() : Collection
    {
        return $this->cache->remember(
            "region.all",
            now()->addMinutes($this->minutes),
            function() {
                return $this->region->all();
            }
        );
    }
    
    /**
     * Cache route binding of Region
     * @param  string $slug [description]
     * @return Region|null       [description]
     */
    public function rememberBySlug(string $slug)
    {
        return $this->cache->remember(
            "region.firstBySlug.{$slug}",
            now()->addMinutes($this->minutes),
            function() use ($slug) {
                return $this->region->makeRepo()->firstBySlug($slug);
            }
        );
    }    
}
