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

class GridComponent extends Component
{
    /**
     *
     * @param Category $category
     * @param ViewFactory $view
     * @param null|int $parent
     * @param int $cols
     * @param bool $categoryCount
     * @param bool $categoryIcon
     * @param bool $childrenCount
     * @param int $childrenLimit
     * @param bool $childrenShuffle
     * @return void
     */
    public function __construct(
        protected Category $category,
        protected ViewFactory $view,
        protected ?int $parent = null,
        protected int $cols = 3,
        protected bool $categoryCount = true,
        protected bool $categoryIcon = true,
        protected bool $childrenCount = true,
        protected int $childrenLimit = 3,
        protected bool $childrenShuffle = false
    ) {
        //
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function render(): View
    {
        return $this->view->make('idir::web.components.category.dir.grid', [
            'categories' => $this->category->makeCache()
                ->rememberWithChildrensByComponent([
                    'parent' => $this->parent,
                    'category_count' => $this->categoryCount,
                    'children_count' => $this->childrenCount,
                    'children_limit' => $this->childrenLimit,
                    'children_shuffle' => $this->childrenShuffle
                ]),
            'cols' => $this->cols,
            'category_icon' => $this->categoryIcon
        ]);
    }
}
