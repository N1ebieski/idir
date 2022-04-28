<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Support\Facades\App;
use N1ebieski\IDir\Cache\BanValueCache;
use N1ebieski\IDir\ValueObjects\BanValue\Type;
use N1ebieski\ICore\Models\BanValue as BaseBanValue;

/**
 * @property Type $type
 */
class BanValue extends BaseBanValue
{
    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->casts['type'] = \N1ebieski\IDir\Casts\Banvalue\TypeCast::class;
    }

    // Factories

    /**
     * [makeCache description]
     * @return BanValueCache [description]
     */
    public function makeCache()
    {
        return App::make(BanValueCache::class, ['banvalue' => $this]);
    }
}
