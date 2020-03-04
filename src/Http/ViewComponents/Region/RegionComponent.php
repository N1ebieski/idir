<?php

namespace N1ebieski\IDir\Http\ViewComponents\Region;

use N1ebieski\IDir\Models\Region\Region;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory as ViewFactory;
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
     * Undocumented variable
     *
     * @var ViewFactory
     */
    protected $view;

    /**
     * Undocumented function
     *
     * @param Region $region
     * @param ViewFactory $view
     */
    public function __construct(Region $region, ViewFactory $view)
    {
        $this->region = $region;

        $this->view = $view;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        return $this->view->make('idir::web.components.region.region', [
            'regions' => $this->region->makeCache()->rememberAll()
        ]);
    }
}
