<?php

namespace N1ebieski\IDir\Cache;

use N1ebieski\IDir\ValueObjects\BanValue\Type;
use N1ebieski\ICore\Cache\BanValueCache as BaseBanValueCache;

class BanValueCache extends BaseBanValueCache
{
    /**
     * [rememberAllWordsAsString description]
     * @return string|null [description]
     */
    public function rememberAllUrlsAsString(): ?string
    {
        return $this->cache->tags('bans.url')->remember(
            "banValue.getAllUrlsAsString",
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () {
                $urls = $this->banValue->where('type', Type::URL)->get();

                return $this->str->escaped($urls->implode('value', '|'));
            }
        );
    }
}
