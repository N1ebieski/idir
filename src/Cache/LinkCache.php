<?php

namespace N1ebieski\IDir\Cache;

use N1ebieski\ICore\Models\Link;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Cache\LinkCache as BaseLinkCache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;

/**
 * [LinkCache description]
 */
class LinkCache extends BaseLinkCache
{
    /**
     * [__construct description]
     * @param Link   $link   [description]
     * @param Cache  $cache  [description]
     * @param Config $config [description]
     */
    public function __construct(Link $link, Cache $cache, Config $config)
    {
        parent::__construct($link, $cache, $config);
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
            now()->addMinutes($this->minutes),
            function() use ($dirs, $component) {
                return $this->link->makeRepo()->getLinksUnionDirsByComponent($dirs, $component);
            }
        );
    }
}
