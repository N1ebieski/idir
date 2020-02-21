<?php

namespace N1ebieski\IDir\Http\ViewComponents\Region\Category;

use N1ebieski\IDir\Models\Region\Region;
use N1ebieski\ICore\Models\Category\Category;
use Illuminate\View\View;
use N1ebieski\IDir\Http\ViewComponents\Region\RegionComponent as BaseRegionComponent;

/**
 * [RegionComponent description]
 */
class RegionComponent extends BaseRegionComponent
{
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    protected $category;

    /**
     * Model
     * @var Region
     */
    protected $region;

    /**
     * Undocumented function
     *
     * @param Category $category
     * @param Region $region
     */
    public function __construct(Category $category, Region $region)
    {
        parent::__construct($region);

        $this->category = $category;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        return view('idir::web.components.region.category.region', [
            'regions' => $this->region->makeCache()->rememberAll(),
            'category' => $this->category,
            'region' => $this->region
        ]);
    }
}
