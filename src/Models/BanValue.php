<?php

namespace N1ebieski\IDir\Models;

use N1ebieski\ICore\Models\BanValue as BaseBanValue;
use N1ebieski\IDir\Cache\BanValueCache;

/**
 * [BanValue description]
 */
class BanValue extends BaseBanValue
{
    // Makers

    /**
     * [makeCache description]
     * @return BanValueCache [description]
     */
    public function makeCache()
    {
        return app()->make(BanValueCache::class, ['banvalue' => $this]);
    }
}
