<?php

namespace N1ebieski\IDir\Cache\Region;

use Illuminate\Support\Carbon;
use N1ebieski\IDir\Models\Region\Region;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;

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
     * Cache driver
     * @var Config
     */
    protected $config;

    /**
     * Undocumented variable
     *
     * @var Carbon
     */
    protected $carbon;

    /**
     * Undocumented function
     *
     * @param Region $region
     * @param Cache $cache
     * @param Config $config
     * @param Carbon $carbon
     */
    public function __construct(Region $region, Cache $cache, Config $config, Carbon $carbon)
    {
        $this->region = $region;

        $this->cache = $cache;
        $this->config = $config;
        $this->carbon = $carbon;
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function rememberAll(): Collection
    {
        return $this->cache->remember(
            "region.all",
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () {
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
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () use ($slug) {
                return $this->region->makeRepo()->firstBySlug($slug);
            }
        );
    }
}
