<?php

namespace N1ebieski\IDir\View\Components\Category\Dir;

use Illuminate\View\View;
use Illuminate\Contracts\Support\Htmlable;
use N1ebieski\IDir\Models\Category\Dir\Category;
use Illuminate\Contracts\View\Factory as ViewFactory;

class CategoryComponent implements Htmlable
{
    /**
     * Model
     * @var Category
     */
    protected $category;

    /**
     * Undocumented variable
     *
     * @var ViewFactory
     */
    protected $view;

    /**
     * Undocumented variable
     *
     * @var bool
     */
    protected $count;

    /**
     * Undocumented variable
     *
     * @var bool
     */
    protected $icon;

    /**
     * Undocumented function
     *
     * @param Category $category
     * @param ViewFactory $view
     * @param boolean $count
     * @param boolean $icon
     */
    public function __construct(
        Category $category,
        ViewFactory $view,
        bool $count = true,
        bool $icon = true
    ) {
        $this->category = $category;

        $this->view = $view;

        $this->count = $count;
        $this->icon = $icon;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml(): View
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
