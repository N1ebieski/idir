<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Field\Group;

use N1ebieski\IDir\Models\Field\Group\Field;
use N1ebieski\IDir\Http\Requests\Admin\Field\Group\IndexRequest;
use N1ebieski\IDir\Http\Requests\Admin\Field\Group\StoreRequest;
use N1ebieski\IDir\Http\Requests\Admin\Field\Group\UpdateRequest;
use N1ebieski\IDir\Filters\Admin\Field\Group\IndexFilter;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Models\Group;

interface Polymorphic
{
    /**
     * [index description]
     * @param  Field        $field   [description]
     * @param  Group        $group   [description]
     * @param  IndexRequest $request [description]
     * @param  IndexFilter  $filter  [description]
     * @return HttpResponse          [description]
     */
    public function index(Field $field, Group $group, IndexRequest $request, IndexFilter $filter): HttpResponse;

    /**
     * [create description]
     * @param  Field        $field [description]
     * @param  Group        $group [description]
     * @return JsonResponse        [description]
     */
    public function create(Field $field, Group $group): JsonResponse;

    /**
     * [store description]
     * @param  Field        $field   [description]
     * @param  StoreRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function store(Field $field, StoreRequest $request): JsonResponse;

    /**
     * [edit description]
     * @param  Field        $field [description]
     * @param  Group        $group [description]
     * @return JsonResponse        [description]
     */
    public function edit(Field $field, Group $group): JsonResponse;

    /**
     * [update description]
     * @param  Field         $field   [description]
     * @param  UpdateRequest $request [description]
     * @return JsonResponse           [description]
     */
    public function update(Field $field, UpdateRequest $request): JsonResponse;
}
