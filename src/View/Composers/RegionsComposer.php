<?php

namespace N1ebieski\IDir\View\Composers;

use N1ebieski\IDir\Models\Region\Region;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\View\Composers\Composer;

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
