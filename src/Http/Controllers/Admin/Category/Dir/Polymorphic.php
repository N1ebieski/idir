<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Category\Dir;

use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\IDir\Http\Requests\Admin\Category\Dir\IndexRequest;
use N1ebieski\IDir\Http\Requests\Admin\Category\Dir\StoreRequest;
use N1ebieski\IDir\Http\Requests\Admin\Category\Dir\StoreGlobalRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\SearchRequest;
use N1ebieski\ICore\Filters\Admin\Category\IndexFilter;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Http\Responses\Admin\Category\SearchResponse;

/**
 * [interface description]
 */
interface Polymorphic
{
    /**
     * Display a listing of the Category.
     *
     * @param  Category      $category      [description]
     * @param  IndexRequest  $request       [description]
     * @param  IndexFilter   $filter        [description]
     * @return View                         [description]
     */
    public function index(Category $category, IndexRequest $request, IndexFilter $filter) : View;

    /**
     * Show the form for creating a new Category.
     *
     * @param  Category      $category      [description]
     * @return JsonResponse
     */
    public function create(Category $category) : JsonResponse;

    /**
     * Store a newly created Category in storage.
     *
     * @param  Category      $category      [description]
     * @param  StoreRequest  $request
     * @return JsonResponse
     */
    public function store(Category $category, StoreRequest $request) : JsonResponse;

    /**
     * Store collection of Categories with childrens in storage.
     *
     * @param  Category      $category      [description]
     * @param  StoreGlobalRequest  $request
     * @return JsonResponse
     */
    public function storeGlobal(Category $category, StoreGlobalRequest $request) : JsonResponse;

    /**
     * Search Categories for specified name.
     *
     * @param  Category      $category [description]
     * @param  SearchRequest $request  [description]
     * @param  SearchResponse $response [description]
     * @return JsonResponse                [description]
     */
    public function search(Category $category, SearchRequest $request, SearchResponse $response) : JsonResponse;
}
