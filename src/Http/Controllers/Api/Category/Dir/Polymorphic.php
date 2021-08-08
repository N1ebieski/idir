<?php

namespace N1ebieski\IDir\Http\Controllers\Api\Category\Dir;

use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\ICore\Filters\Api\Category\IndexFilter;
use N1ebieski\IDir\Http\Requests\Api\Category\Dir\IndexRequest;

interface Polymorphic
{
    /**
     * Display a listing of the Category.
     *
     * @param  Category      $category      [description]
     * @param  IndexRequest  $request       [description]
     * @param  IndexFilter   $filter        [description]
     * @return JsonResponse                 [description]
     */
    public function index(Category $category, IndexRequest $request, IndexFilter $filter): JsonResponse;
}
