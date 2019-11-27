<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Field;

use N1ebieski\IDir\Models\Field\Field;
use N1ebieski\IDir\Http\Requests\Admin\Field\UpdatePositionRequest;
use Illuminate\Http\JsonResponse;

/**
 * [interface description]
 */
interface Polymorphic
{
    /**
     * [editPosition description]
     * @param  Field     $field [description]
     * @return JsonResponse           [description]
     */
    public function editPosition(Field $field) : JsonResponse;

    /**
     * [updatePosition description]
     * @param  Field              $field [description]
     * @param  UpdatePositionRequest $request  [description]
     * @return JsonResponse                    [description]
     */
    public function updatePosition(Field $field, UpdatePositionRequest $request) : JsonResponse;

    /**
     * [destroy description]
     * @param  Field        $field [description]
     * @return JsonResponse        [description]
     */
    public function destroy(Field $field) : JsonResponse;
}
