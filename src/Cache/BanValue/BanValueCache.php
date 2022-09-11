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

namespace N1ebieski\IDir\Cache\BanValue;

use N1ebieski\IDir\ValueObjects\BanValue\Type;
use N1ebieski\ICore\Cache\BanValue\BanValueCache as BaseBanValueCache;

class BanValueCache extends BaseBanValueCache
{
    /**
     * [rememberAllWordsAsString description]
     * @return string [description]
     */
    public function rememberAllUrlsAsString(): string
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
