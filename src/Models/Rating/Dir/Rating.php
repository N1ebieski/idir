<?php

namespace N1ebieski\IDir\Models\Rating\Dir;

use N1ebieski\ICore\Models\Rating\Rating as BaseRating;

class Rating extends BaseRating
{
    // Accessors

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute(): string
    {
        return 'dir';
    }
}
