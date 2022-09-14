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

namespace N1ebieski\IDir\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Utils\Thumbnail\Thumbnail;
use N1ebieski\IDir\Http\Requests\Api\Thumbnail\ReloadRequest;

class ThumbnailController extends Controller
{
    /**
     * @hideFromAPIDocumentation
     *
     * @param ReloadRequest $request
     * @return JsonResponse
     */
    public function reload(ReloadRequest $request, Thumbnail $thumbnail): JsonResponse
    {
        $thumbnail->make($request->input('url'))->reload();

        return Response::json([]);
    }
}
