<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Category\Dir;

use Illuminate\Validation\Rule;
use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\IDir\Models\Category\Dir\Category as DirCategory;
use N1ebieski\ICore\Http\Requests\Admin\Category\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\StoreGlobalRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\SearchRequest;
use N1ebieski\ICore\Filters\Admin\Category\IndexFilter;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Http\Controllers\Admin\Category\Polymorphic;
use N1ebieski\ICore\Http\Controllers\Admin\Category\CategoryController as CategoryBaseController;
use N1ebieski\ICore\Http\Responses\Admin\Category\SearchResponse;

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
     * Display a listing of the Category.
     *
     * @param  Category      $category      [description]
     * @param  IndexRequest  $request       [description]
     * @param  IndexFilter   $filter        [description]
     * @return View                         [description]
     */
    public function index(Category $category, IndexRequest $request, IndexFilter $filter) : View
    {
        $request->validate([
            'filter._parent' => ['nullable', 'integer', Rule::exists('categories', 'id')->where(function($query) {
                $query->where('model_type', $this->category->model_type);
            })],
        ]);

        return parent::index($this->category, $request, $filter);
    }

    /**
     * Show the form for creating a new Category.
     *
     * @param  Category      $category      [description]
     * @return JsonResponse
     */
    public function create(Category $category) : JsonResponse
    {
        return parent::create($this->category);
    }

    /**
     * Store a newly created Category in storage.
     *
     * @param  Category      $category      [description]
     * @param  StoreRequest  $request
     * @return JsonResponse
     */
    public function store(Category $category, StoreRequest $request) : JsonResponse
    {
        $request->validate([
            'parent_id' => ['nullable', Rule::exists('categories', 'id')->where(function($query) {
                $query->where('model_type', $this->category->model_type);
            })],
        ]);

        return parent::store($this->category, $request);
    }

    /**
     * Store collection of Categories with childrens in storage.
     *
     * @param  Category      $category      [description]
     * @param  StoreGlobalRequest  $request
     * @return JsonResponse
     */
    public function storeGlobal(Category $category, StoreGlobalRequest $request) : JsonResponse
    {
        $request->validate([
            'parent_id' => ['nullable', Rule::exists('categories', 'id')->where(function($query) {
                $query->where('model_type', $this->category->model_type);
            })],
        ]);

        return parent::storeGlobal($this->category, $request);
    }

    /**
     * Search Categories for specified name.
     *
     * @param  Category      $category [description]
     * @param  SearchRequest $request  [description]
     * @param  SearchResponse $response [description]
     * @return JsonResponse                [description]
     */
    public function search(Category $category, SearchRequest $request, SearchResponse $response) : JsonResponse
    {
        return parent::search($this->category, $request, $response);
    }
}
