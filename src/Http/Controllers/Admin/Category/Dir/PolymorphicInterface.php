<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Category\Dir;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\ICore\Filters\Admin\Category\IndexFilter;
use N1ebieski\ICore\Http\Requests\Admin\Category\CreateRequest;
use N1ebieski\IDir\Http\Requests\Admin\Category\Dir\IndexRequest;
use N1ebieski\IDir\Http\Requests\Admin\Category\Dir\StoreRequest;
use N1ebieski\IDir\Http\Requests\Admin\Category\Dir\StoreGlobalRequest;

interface PolymorphicInterface
{
    /**
     * Display a listing of the Category.
     *
     * @param  Category      $category      [description]
     * @param  IndexRequest  $request       [description]
     * @param  IndexFilter   $filter        [description]
     * @return HttpResponse                         [description]
     */
    public function index(Category $category, IndexRequest $request, IndexFilter $filter): HttpResponse;

    /**
     * Undocumented function
     *
     * @param Category $category
     * @param CreateRequest $request
     * @return JsonResponse
     */
    public function create(Category $category, CreateRequest $request): JsonResponse;

    /**
     * Store a newly created Category in storage.
     *
     * @param  Category      $category      [description]
     * @param  StoreRequest  $request
     * @return JsonResponse
     */
    public function store(Category $category, StoreRequest $request): JsonResponse;

    /**
     * Store collection of Categories with childrens in storage.
     *
     * @param  Category      $category      [description]
     * @param  StoreGlobalRequest  $request
     * @return JsonResponse
     */
    public function storeGlobal(Category $category, StoreGlobalRequest $request): JsonResponse;
}