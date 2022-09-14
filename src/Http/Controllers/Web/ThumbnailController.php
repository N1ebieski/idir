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

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Utils\Thumbnail\Thumbnail;
use N1ebieski\IDir\Http\Requests\Web\Thumbnail\ShowRequest;

class ThumbnailController extends Controller
{
    /**
     * Undocumented function
     *
     * @param ShowRequest $request
     * @return HttpResponse
     */
    public function show(ShowRequest $request, Thumbnail $thumbnail): HttpResponse
    {
        $thumbnail = $thumbnail->make($request->input('url'));

        return Response::make($thumbnail->generate(), HttpResponse::HTTP_OK, ['Content-Type' => 'image'])
            ->setMaxAge(Config::get('idir.dir.thumbnail.cache.days') * 24 * 60 * 60)
            ->setLastModified(
                Carbon::createFromTimestamp($thumbnail->getLastModified())->toDateTime()
            )
            ->setPublic();
    }
}
