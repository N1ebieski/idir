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

namespace N1ebieski\IDir\Http\Controllers\Api\Category\Dir;

use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\ICore\Filters\Api\Category\IndexFilter;
use N1ebieski\IDir\Http\Requests\Api\Category\Dir\IndexRequest;
use N1ebieski\ICore\Http\Controllers\Api\Category\CategoryController as BaseCategoryController;

/**
 * @group Categories
 *
 * > Routes:
 *
 *     /routes/vendor/icore/api/categories.php
 *     /routes/vendor/idir/api/categories.php
 *
 * > Controllers:
 *
 *     N1ebieski\ICore\Http\Controllers\Api\Category\CategoryController
 *     N1ebieski\ICore\Http\Controllers\Api\Category\Post\CategoryController
 *     N1ebieski\IDir\Http\Controllers\Api\Category\Dir\CategoryController
 *
 * > Resource:
 *
 *     N1ebieski\ICore\Http\Resources\Category\CategoryResource
 *
 * Permissions:
 *
 * - api.* - access to all api endpoints
 * - api.categories.* - access to all categories endpoints
 * - api.categories.view - access to endpoints with collection of categories
 */
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
     * Index of dir's categories
     *
     * @bodyParam filter.status int Must be one of 1 or 0 (available only for admin.categories.view). Example: 1
     *
     * @apiResourceCollection N1ebieski\ICore\Http\Resources\Category\CategoryResource
     * @apiResourceModel N1ebieski\IDir\Models\Category\Dir\Category states=active,sentence
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
