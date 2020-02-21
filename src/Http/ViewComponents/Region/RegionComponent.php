<?php

namespace N1ebieski\IDir\Http\ViewComponents\Region;

use N1ebieski\IDir\Models\Region\Region;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\View\View;

/**
 * [RegionComponent description]
 */
class RegionComponent implements Htmlable
{
    /**
     * Model
     * @var Region
     */
    protected $region;

    /**
     * Undocumented function
     *
     * @param Region $region
     */
    public function __construct(Region $region)
    {
        $this->region = $region;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        return view('idir::web.components.region.region', [
            'regions' => $this->region->makeCache()->rememberAll()
        ]);
    }
}
