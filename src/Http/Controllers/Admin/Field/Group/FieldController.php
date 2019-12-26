<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Field\Group;

use N1ebieski\IDir\Models\Field\Group\Field;
use N1ebieski\IDir\Http\Controllers\Admin\Field\FieldController as BaseFieldController;
use N1ebieski\IDir\Http\Requests\Admin\Field\Group\IndexRequest;
use N1ebieski\IDir\Http\Requests\Admin\Field\Group\StoreRequest;
use N1ebieski\IDir\Http\Requests\Admin\Field\Group\UpdateRequest;
use N1ebieski\IDir\Filters\Admin\Field\Group\IndexFilter;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Http\Controllers\Admin\Field\Group\Polymorphic;

/**
 * [FieldController description]
 */
class FieldController extends BaseFieldController implements Polymorphic
{
    /**
     * [index description]
     * @param  Field        $field   [description]
     * @param  Group        $group   [description]
     * @param  IndexRequest $request [description]
     * @param  IndexFilter  $filter  [description]
     * @return View                  [description]
     */
    public function index(Field $field, Group $group, IndexRequest $request, IndexFilter $filter) : View
    {
        return view("idir::admin.field.group.index", [
            'field' => $field,
            'fields' => $field->makeRepo()->paginateByFilter($filter->all() + [
                'except' => $request->input('except')
            ]),
            'groups' => $group->makeRepo()->all(),
            'filter' => $filter->all(),
            'paginate' => config('database.paginate')
        ]);
    }

    /**
     * [create description]
     * @param  Field        $field [description]
     * @param  Group        $group [description]
     * @return JsonResponse        [description]
     */
    public function create(Field $field, Group $group) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('idir::admin.field.group.create', [
                'field' => $field,
                'groups' => $group->makeRepo()->all()
            ])->render()
        ]);
    }

    /**
     * [store description]
     * @param  Field        $field   [description]
     * @param  StoreRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function store(Field $field, StoreRequest $request) : JsonResponse
    {
        $field->makeService()->create($request->validated());

        $request->session()->flash('success', trans('idir::fields.success.store'));

        return response()->json(['success' => '' ]);
    }

    /**
     * [edit description]
     * @param  Field        $field [description]
     * @param  Group        $group [description]
     * @return JsonResponse        [description]
     */
    public function edit(Field $field, Group $group) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('idir::admin.field.group.edit', [
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
    public function update(Field $field, UpdateRequest $request) : JsonResponse
    {
        $field->makeService()->update($request->validated());

        return response()->json([
            'success' => '',
            'view' => view('idir::admin.field.partials.field', [
                'field' => $field
            ])->render()
        ]);
    }
}
