<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\View\Components\Region\Category;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use N1ebieski\IDir\Models\Region\Region;
use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Contracts\View\Factory as ViewFactory;

class RegionComponent extends Component
{
    /**
     * Undocumented function
     *
     * @param Category $category
     * @param Region $region
     * @param ViewFactory $view
     */
    public function __construct(
        protected Category $category,
        protected Region $region,
        protected ViewFactory $view
    ) {
        //
    }

    /**
     *
     * @return View
     */
    public function render(): View
    {
        return $this->view->make('idir::web.components.region.category.region', [
            'regions' => $this->region->makeCache()->rememberAll(),
            'category' => $this->category,
            'region' => $this->region
        ]);
    }
}
