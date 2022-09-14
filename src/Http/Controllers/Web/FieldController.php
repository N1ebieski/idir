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

namespace N1ebieski\IDir\Http\Controllers\Web;

use GusApi\GusApi;
use Illuminate\Http\JsonResponse;
use GusApi\Exception\NotFoundException;
use N1ebieski\IDir\Http\Requests\Web\Field\GusRequest;
use N1ebieski\IDir\Http\Responses\Web\Field\GusResponse;

class FieldController
{
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
