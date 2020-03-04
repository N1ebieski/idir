<?php

namespace N1ebieski\IDir\Http\ViewComponents\Category\Dir;

use N1ebieski\IDir\Models\Category\Dir\Category;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\View\View;

/**
 * [CategoryComponent description]
 */
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
     * Undocumented function
     *
     * @param Category $category
     * @param ViewFactory $view
     */
    public function __construct(Category $category, ViewFactory $view)
    {
        $this->category = $category;

        $this->view = $view;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        return $this->view->make('idir::web.components.category.dir.category', [
            'categories' => $this->category->makeCache()->rememberRootsWithNestedMorphsCount()
        ]);
    }
}
