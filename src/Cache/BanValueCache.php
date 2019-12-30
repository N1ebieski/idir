<?php

namespace N1ebieski\IDir\Cache;

use N1ebieski\ICore\Models\BanValue;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Str;
use N1ebieski\ICore\Cache\BanValueCache as BaseBanValueCache;

/**
 * [BanValueCache description]
 */
class BanValueCache extends BaseBanValueCache
{
    /**
     * [__construct description]
     * @param BanValue $banValue [description]
     * @param Cache    $cache    [description]
     * @param Config   $config   [description]
     * @param Str      $str      [description]
     */
    public function __construct(BanValue $banValue, Cache $cache, Config $config, Str $str)
    {
        parent::__construct($banValue, $cache, $config, $str);
    }

    /**
     * [rememberAllWordsAsString description]
     * @return string|null [description]
     */
    public function rememberAllUrlsAsString() : ?string
    {
        return $this->cache->tags('bans.url')->remember(
            "banValue.getAllUrlsAsString",
            now()->addMinutes($this->minutes),
            function() {
                $words = $this->banValue->whereType('url')->get();

                return $this->str->escaped($words->implode('value', '|'));
            }
        );
    }
}
