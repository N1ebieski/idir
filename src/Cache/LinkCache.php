<?php

namespace N1ebieski\IDir\Cache;

use N1ebieski\IDir\Models\Link;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Cache\LinkCache as BaseLinkCache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Carbon;

/**
 * [LinkCache description]
 */
class LinkCache extends BaseLinkCache
{
    /**
     * Link model
     * @var Link
     */
    protected $link;

    /**
     * Undocumented function
     *
     * @param Link $link
     * @param Cache $cache
     * @param Config $config
     * @param Carbon $carbon
     */
    public function __construct(Link $link, Cache $cache, Config $config, Carbon $carbon)
    {
        parent::__construct($link, $cache, $config, $carbon);
    }

    /**
     * [rememberLinksUnionDirsByComponent description]
     * @param  Builder|null    $dirs      [description]
     * @param  array      $component [description]
     * @return Collection            [description]
     */
    public function rememberLinksUnionDirsByComponent(Builder $dirs = null, array $component) : Collection
    {
        $cats = $component['cats'] !== null ? implode('.', $component['cats']) : null;

        return $this->cache->tags(['links'])->remember(
            "link.getLinksUnionDirsByComponent.{$cats}",
            $this->carbon->now()->addMinutes($this->minutes),
            function () use ($dirs, $component) {
                return $this->link->makeRepo()->getLinksUnionDirsByComponent($dirs, $component);
            }
        );
    }
}
