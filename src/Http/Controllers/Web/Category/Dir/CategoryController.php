<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Category\Dir;

use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\IDir\Models\Category\Dir\Category as DirCategory;
use N1ebieski\ICore\Http\Requests\Web\Category\SearchRequest;
use N1ebieski\ICore\Http\Responses\Web\Category\SearchResponse;
use N1ebieski\ICore\Http\Controllers\Web\Category\CategoryController as CategoryBaseController;
use N1ebieski\IDir\Http\Controllers\Web\Category\Dir\Polymorphic;
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
     * @param  SearchResponse $response  [description]
     * @return JsonResponse
     */
    public function search(Category $category, SearchRequest $request, SearchResponse $response) : JsonResponse
    {
        return parent::search($this->category, $request, $response);
    }
}
