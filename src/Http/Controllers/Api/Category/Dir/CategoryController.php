<?php

namespace N1ebieski\IDir\Http\Controllers\Api\Category\Dir;

use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\ICore\Filters\Api\Category\IndexFilter;
use N1ebieski\IDir\Http\Requests\Api\Category\Dir\IndexRequest;
use N1ebieski\ICore\Http\Controllers\Api\Category\CategoryController as BaseCategoryController;

class CategoryController implements Polymorphic
{
    /**
     * Undocumented variable
     *
     * @var BaseCategoryController
     */
    protected $decorated;

    /**
     * Undocumented function
     *
     * @param BaseCategoryController $decorated
     */
    public function __construct(BaseCategoryController $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * Display a listing of the Category.
     *
     * @param  Category      $category      [description]
     * @param  IndexRequest  $request       [description]
     * @param  IndexFilter   $filter        [description]
     * @return JsonResponse                 [description]
     */
    public function index(Category $category, IndexRequest $request, IndexFilter $filter): JsonResponse
    {
        return $this->decorated->index($category, $request, $filter);
    }
}
