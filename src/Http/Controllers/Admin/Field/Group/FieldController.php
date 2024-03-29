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

namespace N1ebieski\IDir\Http\Controllers\Admin\Field\Group;

use N1ebieski\IDir\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Models\Field\Group\Field;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Filters\Admin\Field\Group\IndexFilter;
use N1ebieski\IDir\Http\Requests\Admin\Field\Group\IndexRequest;
use N1ebieski\IDir\Http\Requests\Admin\Field\Group\StoreRequest;
use N1ebieski\IDir\Http\Requests\Admin\Field\Group\UpdateRequest;
use N1ebieski\IDir\Http\Controllers\Admin\Field\Group\Polymorphic;
use N1ebieski\IDir\Http\Controllers\Admin\Field\FieldController as BaseFieldController;

class FieldController extends BaseFieldController implements Polymorphic
{
    /**
     * [index description]
     * @param  Field        $field   [description]
     * @param  Group        $group   [description]
     * @param  IndexRequest $request [description]
     * @param  IndexFilter  $filter  [description]
     * @return HttpResponse                  [description]
     */
    public function index(Field $field, Group $group, IndexRequest $request, IndexFilter $filter): HttpResponse
    {
        return Response::view("idir::admin.field.group.index", [
            'field' => $field,
            'fields' => $field->makeRepo()->paginateByFilter($filter->all()),
            'groups' => $group->all(),
            'filter' => $filter->all(),
            'paginate' => Config::get('database.paginate')
        ]);
    }

    /**
     * [create description]
     * @param  Field        $field [description]
     * @param  Group        $group [description]
     * @return JsonResponse        [description]
     */
    public function create(Field $field, Group $group): JsonResponse
    {
        return Response::json([
            'view' => View::make('idir::admin.field.group.create', [
                'field' => $field,
                'groups' => $group->all()
            ])->render()
        ]);
    }

    /**
     * [store description]
     * @param  Field        $field   [description]
     * @param  StoreRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function store(Field $field, StoreRequest $request): JsonResponse
    {
        $field->makeService()->create($request->validated());

        $request->session()->flash('success', trans('idir::fields.success.store'));

        return Response::json([
            'redirect' => URL::route("admin.field.group.index", [
                'filter' => [
                    'search' => "id:\"{$field->id}\""
                ]
            ])
        ]);
    }

    /**
     * [edit description]
     * @param  Field        $field [description]
     * @param  Group        $group [description]
     * @return JsonResponse        [description]
     */
    public function edit(Field $field, Group $group): JsonResponse
    {
        return Response::json([
            'view' => View::make('idir::admin.field.group.edit', [
                'field' => $field,
                'groups' => $group->makeRepo()->getWithField($field->id)
            ])->render()
        ]);
    }

    /**
     * [update description]
     * @param  Field         $field   [description]
     * @param  UpdateRequest $request [description]
     * @return JsonResponse           [description]
     */
    public function update(Field $field, UpdateRequest $request): JsonResponse
    {
        $field->makeService()->update($request->validated());

        return Response::json([
            'view' => View::make('idir::admin.field.partials.field', [
                'field' => $field
            ])->render()
        ]);
    }
}
