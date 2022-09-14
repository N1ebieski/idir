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

namespace N1ebieski\IDir\Http\Controllers\Admin\Field;

use GusApi\GusApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\View;
use N1ebieski\IDir\Models\Field\Field;
use GusApi\Exception\NotFoundException;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Http\Requests\Admin\Field\GusRequest;
use N1ebieski\IDir\Http\Responses\Admin\Field\GusResponse;
use N1ebieski\IDir\Http\Controllers\Admin\Field\Polymorphic;
use N1ebieski\IDir\Http\Requests\Admin\Field\DestroyRequest;
use N1ebieski\IDir\Http\Requests\Admin\Field\UpdatePositionRequest;

class FieldController implements Polymorphic
{
    /**
     * [editPosition description]
     * @param  Field     $field [description]
     * @return JsonResponse           [description]
     */
    public function editPosition(Field $field): JsonResponse
    {
        return Response::json([
            'view' => View::make('idir::admin.field.edit_position', [
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
    public function updatePosition(Field $field, UpdatePositionRequest $request): JsonResponse
    {
        $field->makeService()->updatePosition($request->input('position'));

        return Response::json([
            'siblings' => $field->makeRepo()->getSiblingsAsArray()
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Field $field
     * @param DestroyRequest $request
     * @return JsonResponse
     */
    public function destroy(Field $field, DestroyRequest $request): JsonResponse
    {
        $field->delete();

        return Response::json([]);
    }

    /**
     * Undocumented function
     *
     * @param GusRequest $request
     * @param GusApi $gusApi
     * @return JsonResponse
     */
    public function gus(GusRequest $request, GusResponse $response, GusApi $gusApi): JsonResponse
    {
        try {
            $method = 'getBy' . ucfirst($request->input('type'));

            $gusApi->login();
            $gusReport = $gusApi->$method($request->input('number'))[0];
        } catch (NotFoundException $e) {
            $gusReport = null;
        }

        return $response->makeResponse($gusReport);
    }
}
