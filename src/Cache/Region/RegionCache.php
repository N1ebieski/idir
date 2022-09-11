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

namespace N1ebieski\IDir\Cache\Region;

use Illuminate\Support\Carbon;
use N1ebieski\IDir\Models\Region\Region;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;

class RegionCache
{
    /**
     * Undocumented function
     *
     * @param Region $region
     * @param Cache $cache
     * @param Config $config
     * @param Carbon $carbon
     */
    public function __construct(
        protected Region $region,
        protected Cache $cache,
        protected Config $config,
        protected Carbon $carbon
    ) {
        //
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
