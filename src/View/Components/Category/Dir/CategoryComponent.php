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

namespace N1ebieski\IDir\View\Components\Category\Dir;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use N1ebieski\IDir\Models\Category\Dir\Category;
use Illuminate\Contracts\View\Factory as ViewFactory;

class CategoryComponent extends Component
{
    /**
     * Undocumented function
     *
     * @param Category $category
     * @param ViewFactory $view
     * @param boolean $count
     * @param boolean $icon
     */
    public function __construct(
        protected Category $category,
        protected ViewFactory $view,
        protected bool $count = true,
        protected bool $icon = true
    ) {
        //
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function render(): View
    {
        return $this->view->make('idir::web.components.category.dir.category', [
            'categories' => $this->category->makeCache()
                ->rememberRootsByComponent([
                    'count' => $this->count
                ]),
            'icon' => $this->icon
        ]);
    }
}
