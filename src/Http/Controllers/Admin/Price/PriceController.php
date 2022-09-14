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

namespace N1ebieski\IDir\Http\Controllers\Admin\Price;

use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Loads\Admin\Price\EditLoad;
use N1ebieski\IDir\Filters\Admin\Price\IndexFilter;
use N1ebieski\IDir\Http\Requests\Admin\Price\IndexRequest;
use N1ebieski\IDir\Http\Requests\Admin\Price\StoreRequest;
use N1ebieski\IDir\Http\Requests\Admin\Price\CreateRequest;
use N1ebieski\IDir\Http\Requests\Admin\Price\UpdateRequest;

class PriceController
{
    /**
     * Undocumented function
     *
     * @param Price $price
     * @param Group $group
     * @param IndexRequest $request
     * @param IndexFilter $filter
     * @return HttpResponse
     */
    public function index(Price $price, Group $group, IndexRequest $request, IndexFilter $filter): HttpResponse
    {
        return Response::view('idir::admin.price.index', [
            'prices' => $price->makeRepo()->paginateByFilter($filter->all()),
            'groups' => $group->makeRepo()->getExceptDefault(),
            'filter' => $filter->all(),
            'paginate' => Config::get('database.paginate')
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Price $price
     * @param Group $group
     * @param CreateRequest $request
     * @return JsonResponse
     */
    public function create(Price $price, Group $group, CreateRequest $request): JsonResponse
    {
        return Response::json([
            'view' => View::make('idir::admin.price.create', [
                'price' => $price,
                'groups' => $group->makeRepo()->getExceptDefault(),
                'group_id' => (int)$request->input('group_id')
            ])->render()
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Price $price
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(Price $price, StoreRequest $request): JsonResponse
    {
        $price->makeService()->create($request->validated());

        $request->session()->flash('success', trans('idir::prices.success.store'));

        return Response::json([]);
    }

    /**
     * Undocumented function
     *
     * @param Price $price
     * @param Group $group
     * @param EditLoad $load
     * @return JsonResponse
     */
    public function edit(Price $price, Group $group, EditLoad $load): JsonResponse
    {
        return Response::json([
            'view' => View::make('idir::admin.price.edit', [
                'price' => $price,
                'groups' => $group->makeRepo()->getExceptDefault()
            ])->render()
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Price $price
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(Price $price, UpdateRequest $request): JsonResponse
    {
        $price->makeService()->update($request->validated());

        return Response::json([
            'view' => View::make('idir::admin.price.partials.price', [
                'price' => $price
            ])->render()
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Price $price
     * @return JsonResponse
     */
    public function destroy(Price $price): JsonResponse
    {
        $price->makeService()->delete();

        return Response::json([]);
    }
}
