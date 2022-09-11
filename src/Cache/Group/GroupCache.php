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

namespace N1ebieski\IDir\Cache\Group;

use Illuminate\Support\Carbon;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\ValueObjects\Group\Slug;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;

class GroupCache
{
    /**
     * Undocumented function
     *
     * @param Group $group
     * @param Cache $cache
     * @param Config $config
     * @param Carbon $carbon
     */
    public function __construct(
        protected Group $group,
        protected Cache $cache,
        protected Config $config,
        protected Carbon $carbon
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @return Group|null
     */
    public function rememberBySlug(Slug $slug): ?Group
    {
        return $this->cache->tags(["group.{$slug->getValue()}"])->remember(
            "group.firstBySlug.{$slug->getValue()}",
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () use ($slug) {
                return $this->group->makeRepo()->firstBySlug($slug);
            }
        );
    }
}
