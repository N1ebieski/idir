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

namespace N1ebieski\IDir\Cache\Link;

use N1ebieski\IDir\Models\Link;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\Cache\Link\LinkCache as BaseLinkCache;

/**
 * @property Link $link
 *
 */
class LinkCache extends BaseLinkCache
{
    /**
     * [rememberLinksUnionDirsByComponent description]
     * @param  Builder|null    $dirs      [description]
     * @param  array      $component [description]
     * @return Collection            [description]
     */
    public function rememberLinksUnionDirsByComponent(Builder $dirs = null, array $component): Collection
    {
        $json = json_encode($component);

        return $this->cache->tags(['links'])->remember(
            "link.getLinksUnionDirsByComponent.{$json}",
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () use ($dirs, $component) {
                return $this->link->makeRepo()->getLinksUnionDirsByComponent($dirs, $component);
            }
        );
    }
}
