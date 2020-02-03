<?php

namespace N1ebieski\IDir\Repositories;

use N1ebieski\IDir\Models\Region\Region;
use Illuminate\Contracts\Config\Repository as Config;

/**
 * [RegionRepo description]
 */
class RegionRepo
{
    /**
     * [private description]
     * @var Region
     */
    protected $region;

    /**
     * [protected description]
     * @var int
     */
    protected $paginate;

    /**
     * [__construct description]
     * @param Region $region [description]
     * @param Config   $config   [description]
     */
    public function __construct(Region $region, Config $config)
    {
        $this->region = $region;

        $this->paginate = $config->get('database.paginate');
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
