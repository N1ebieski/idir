<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Field;

use N1ebieski\IDir\Models\Field\Field;
use N1ebieski\IDir\Http\Requests\Admin\Field\UpdatePositionRequest;
use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Http\Controllers\Admin\Field\Polymorphic;

/**
 * [FieldController description]
 */
class FieldController implements Polymorphic
{
    /**
     * [editPosition description]
     * @param  Field     $field [description]
     * @return JsonResponse           [description]
     */
    public function editPosition(Field $field) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('idir::admin.field.edit_position', [
                'field' => $field,
                'siblings_count' => $field->countSiblings()
            ])->render()
        ]);
    }

    /**
     * [updatePosition description]
     * @param  Field              $field [description]
     * @param  UpdatePositionRequest $request  [description]
     * @return JsonResponse                    [description]
     */
    public function updatePosition(Field $field, UpdatePositionRequest $request) : JsonResponse
    {
        $field->makeService()->updatePosition($request->only('position'));

        return response()->json([
            'success' => '',
            'siblings' => $field->makeRepo()->getSiblingsAsArray()
        ]);
    }

    /**
     * [destroy description]
     * @param  Field        $field [description]
     * @return JsonResponse        [description]
     */
    public function destroy(Field $field) : JsonResponse
    {
        $field->delete();

        return response()->json(['success' => '']);
    }
}
