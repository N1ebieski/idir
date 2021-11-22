<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Support\Facades\App;
use N1ebieski\IDir\Cache\BanValueCache;
use N1ebieski\ICore\Models\BanValue as BaseBanValue;

class BanValue extends BaseBanValue
{
    // Makers

    /**
     * [makeCache description]
     * @return BanValueCache [description]
     */
    public function makeCache()
    {
        return App::make(BanValueCache::class, ['banvalue' => $this]);
    }
}
