<?php

namespace N1ebieski\iDir\Http\Controllers\Web\Category\Dir;

use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\IDir\Models\Category\Dir\Category as DirCategory;
use N1ebieski\ICore\Http\Requests\Web\Category\SearchRequest;
use N1ebieski\ICore\Http\Controllers\Web\Category\CategoryController as CategoryBaseController;
use N1ebieski\ICore\Http\Controllers\Web\Category\Polymorphic;
use Illuminate\Http\JsonResponse;

/**
 * [CategoryController description]
 */
class CategoryController extends CategoryBaseController implements Polymorphic
{
    /**
     * [__construct description]
     * @param DirCategory        $category        [description]
     */
    public function __construct(DirCategory $category)
    {
        parent::__construct($category);
    }

    /**
     * Search Categories for specified name.
     *
     * @param  Category      $category [description]
     * @param  SearchRequest $request  [description]
     * @return JsonResponse
     */
    public function search(Category $category, SearchRequest $request) : JsonResponse
    {
        return parent::search($this->category, $request);
    }
}
