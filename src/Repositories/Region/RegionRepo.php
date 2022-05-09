<?php

namespace N1ebieski\IDir\Repositories\Region;

use N1ebieski\IDir\Models\Region\Region;

class RegionRepo
{
    /**
     * [private description]
     * @var Region
     */
    protected $region;

    /**
     * [__construct description]
     * @param Region $region [description]
     */
    public function __construct(Region $region)
    {
        $this->region = $region;
    }

    /**
     * [firstBySlug description]
     * @param  string $slug [description]
     * @return Category|null       [description]
     */
    public function firstBySlug(string $slug)
    {
        return $this->region->where('slug', $slug)->first();
    }
}
