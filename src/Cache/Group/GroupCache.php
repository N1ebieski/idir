<?php

namespace N1ebieski\IDir\Cache\Group;

use Illuminate\Support\Carbon;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\ValueObjects\Group\Slug;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;

class GroupCache
{
    /**
     * Group model
     * @var Group
     */
    protected $group;

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
     * @param Group $group
     * @param Cache $cache
     * @param Config $config
     * @param Carbon $carbon
     */
    public function __construct(Group $group, Cache $cache, Config $config, Carbon $carbon)
    {
        $this->group = $group;

        $this->cache = $cache;
        $this->config = $config;
        $this->carbon = $carbon;
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
