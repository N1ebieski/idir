<?php

namespace N1ebieski\IDir\Http\ViewComponents\Category\Dir;

use N1ebieski\IDir\Models\Category\Dir\Category;
use Illuminate\Contracts\Support\Htmlable;
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
     * [__construct description]
     * @param Category      $category      [description]
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        return view('idir::web.components.category.dir.category', [
            'categories' => $this->category->makeCache()->rememberRootsWithNestedMorphsCount()
        ]);
    }
}
