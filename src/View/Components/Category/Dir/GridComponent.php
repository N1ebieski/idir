<?php

namespace N1ebieski\IDir\View\Components\Category\Dir;

use N1ebieski\IDir\Models\Category\Dir\Category;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\View\View;

class GridComponent implements Htmlable
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
     * @var int
     */
    protected $parent;

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected $cols;

    /**
     * Undocumented variable
     *
     * @var bool
     */
    protected $category_count;

    /**
     * Undocumented variable
     *
     * @var bool
     */
    protected $category_icon;

    /**
     * Undocumented variable
     *
     * @var bool
     */
    protected $children_count;

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected $children_limit;

    /**
     * Undocumented variable
     *
     * @var bool
     */
    protected $children_shuffle;

    /**
     * Undocumented function
     *
     * @param Category $category
     * @param ViewFactory $view
     */
    public function __construct(
        Category $category,
        ViewFactory $view,
        int $parent = null,
        int $cols = 3,
        bool $category_count = true,
        bool $category_icon = true,
        bool $children_count = true,
        int $children_limit = 3,
        bool $children_shuffle = false
    ) {
        $this->category = $category;

        $this->view = $view;

        $this->parent = $parent;
        $this->cols = $cols;
        $this->category_count = $category_count;
        $this->category_icon = $category_icon;
        $this->children_count = $children_count;
        $this->children_limit = $children_limit;
        $this->children_shuffle = $children_shuffle;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        return $this->view->make('idir::web.components.category.dir.grid', [
            'categories' => $this->category->makeCache()
                ->rememberWithChildrensByComponent([
                    'parent' => $this->parent,
                    'category_count' => $this->category_count,
                    'children_count' => $this->children_count,
                    'children_limit' => $this->children_limit,
                    'children_shuffle' => $this->children_shuffle
                ]),
            'cols' => $this->cols,
            'category_icon' => $this->category_icon
        ]);
    }
}
