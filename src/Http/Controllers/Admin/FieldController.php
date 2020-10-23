<?php

namespace N1ebieski\IDir\Http\Controllers\Admin;

use GusApi\GusApi;
use Illuminate\Http\JsonResponse;
use GusApi\Exception\NotFoundException;
use Illuminate\Support\Facades\App;
use N1ebieski\IDir\Http\Requests\Web\Field\GusRequest;
use N1ebieski\IDir\Http\Responses\Field\GusResponse;

class FieldController
{
    /**
     * Undocumented function
     *
     * @param GusRequest $request
     * @param GusApi $gusApi
     * @return JsonResponse
     */
    public function gus(GusRequest $request, GusApi $gusApi) : JsonResponse
    {
        try {
            $method = 'getBy' . ucfirst($request->input('type'));

            $gusApi->login();
            $gusReport = $gusApi->$method($request->input('number'))[0];
        } catch (NotFoundException $e) {
            $gusReport = null;
        }

        return App::make(GusResponse::class, ['gusReport' => $gusReport])->makeResponse();
    }
}
