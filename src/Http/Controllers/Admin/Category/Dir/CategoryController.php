<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Http\Controllers\Admin\Category\Dir;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\ICore\Filters\Admin\Category\IndexFilter;
use N1ebieski\ICore\Http\Requests\Admin\Category\CreateRequest;
use N1ebieski\IDir\Http\Requests\Admin\Category\Dir\IndexRequest;
use N1ebieski\IDir\Http\Requests\Admin\Category\Dir\StoreRequest;
use N1ebieski\IDir\Http\Requests\Admin\Category\Dir\StoreGlobalRequest;
use N1ebieski\IDir\Http\Controllers\Admin\Category\Dir\PolymorphicInterface;
use N1ebieski\ICore\Http\Controllers\Admin\Category\CategoryController as BaseCategoryController;

class CategoryController implements PolymorphicInterface
{
    /**
     *
     * @param BaseCategoryController $decorated
     * @return void
     */
    public function __construct(protected BaseCategoryController $decorated)
    {
        //
    }


    /**
     * Display a listing of the Category.
     *
     * @param  Category      $category      [description]
     * @param  IndexRequest  $request       [description]
     * @param  IndexFilter   $filter        [description]
     * @return HttpResponse                         [description]
     */
    public function index(Category $category, IndexRequest $request, IndexFilter $filter): HttpResponse
    {
        return $this->decorated->index($category, $request, $filter);
    }

    /**
     * Undocumented function
     *
     * @param Category $category
     * @param CreateRequest $request
     * @return JsonResponse
     */
    public function create(Category $category, CreateRequest $request): JsonResponse
    {
        return $this->decorated->create($category, $request);
    }

    /**
     * Store a newly created Category in storage.
     *
     * @param  Category      $category      [description]
     * @param  StoreRequest  $request
     * @return JsonResponse
     */
    public function store(Category $category, StoreRequest $request): JsonResponse
    {
        return $this->decorated->store($category, $request);
    }

    /**
     * Store collection of Categories with childrens in storage.
     *
     * @param  Category      $category      [description]
     * @param  StoreGlobalRequest  $request
     * @return JsonResponse
     */
    public function storeGlobal(Category $category, StoreGlobalRequest $request): JsonResponse
    {
        return $this->decorated->storeGlobal($category, $request);
    }
}
