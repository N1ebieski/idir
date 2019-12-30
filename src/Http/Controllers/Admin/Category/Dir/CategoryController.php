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
use N1ebieski\IDir\Http\Controllers\Admin\Category\Dir\Polymorphic;
use N1ebieski\ICore\Http\Controllers\Admin\Category\CategoryController as CategoryBaseController;
use N1ebieski\ICore\Http\Responses\Admin\Category\SearchResponse;

/**
 * [CategoryController description]
 */
class CategoryController extends CategoryBaseController implements Polymorphic
{
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
        $categoryService = $category->makeService();

        return view('icore::admin.category.index', [
            'model' => $category,
            'categories' => $categoryService->paginateByFilter($filter->all() + [
                'except' => $request->input('except')
            ]),
            'parents' => $categoryService->getAsFlatTree(),
            'filter' => $filter->all(),
            'paginate' => config('database.paginate')
        ]);
    }

    /**
     * Show the form for creating a new Category.
     *
     * @param  Category      $category      [description]
     * @return JsonResponse
     */
    public function create(Category $category) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('icore::admin.category.create', [
                'model' => $category,
                'categories' => $category->makeService()->getAsFlatTree()
            ])->render()
        ]);
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
        $category->makeService()->create($request->only(['name', 'icon', 'parent_id']));

        $request->session()->flash('success', trans('icore::categories.success.store'));

        return response()->json(['success' => '' ]);
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
        $category->makeService()->createGlobal($request->only(['names', 'parent_id', 'clear']));

        $request->session()->flash('success', trans('icore::categories.success.store_global'));

        return response()->json(['success' => '' ]);
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
        $categories = $category->makeRepo()->getBySearch($request->get('name'));

        return $response->setCategories($categories)->makeResponse();
    }
}
