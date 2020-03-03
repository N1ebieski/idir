<?php

namespace N1ebieski\IDir\Cache;

use N1ebieski\ICore\Models\BanValue;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use N1ebieski\ICore\Cache\BanValueCache as BaseBanValueCache;

/**
 * [BanValueCache description]
 */
class BanValueCache extends BaseBanValueCache
{
    /**
     * Undocumented function
     *
     * @param BanValue $banValue
     * @param Cache $cache
     * @param Config $config
     * @param Str $str
     * @param Carbon $carbon
     */
    public function __construct(
        BanValue $banValue,
        Cache $cache,
        Config $config,
        Str $str,
        Carbon $carbon
    ) {
        parent::__construct($banValue, $cache, $config, $str, $carbon);
    }

    /**
     * [rememberAllWordsAsString description]
     * @return string|null [description]
     */
    public function rememberAllUrlsAsString() : ?string
    {
        return $this->cache->tags('bans.url')->remember(
            "banValue.getAllUrlsAsString",
            $this->carbon->now()->addMinutes($this->minutes),
            function () {
                $urls = $this->banValue->whereType('url')->get();

                return $this->str->escaped($urls->implode('value', '|'));
            }
        );
    }
}
