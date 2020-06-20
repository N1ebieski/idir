<?php

namespace N1ebieski\IDir\View\Composers;

use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\View\Composers\Composer;
use N1ebieski\IDir\Models\Region\Region;

class RegionsComposer extends Composer
{
    /**
     * Undocumented variable
     *
     * @var Collection
     */
    public $regions;

    /**
     * Undocumented function
     *
     * @param Region $region
     */
    public function __construct(Region $region)
    {
        $this->regions = $region->makeCache()->rememberAll();
    }
}
