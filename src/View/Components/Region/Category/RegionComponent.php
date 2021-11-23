<?php

namespace N1ebieski\IDir\View\Components\Region\Category;

use Illuminate\View\View;
use N1ebieski\IDir\Models\Region\Region;
use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Contracts\View\Factory as ViewFactory;
use N1ebieski\IDir\View\Components\Region\RegionComponent as BaseRegionComponent;

class RegionComponent extends BaseRegionComponent
{
    /**
     * Undocumented variable
     *
     * @var Category
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
     * @param ViewFactory $view
     */
    public function __construct(Category $category, Region $region, ViewFactory $view)
    {
        parent::__construct($region, $view);

        $this->category = $category;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml(): View
    {
        return $this->view->make('idir::web.components.region.category.region', [
            'regions' => $this->region->makeCache()->rememberAll(),
            'category' => $this->category,
            'region' => $this->region
        ]);
    }
}
