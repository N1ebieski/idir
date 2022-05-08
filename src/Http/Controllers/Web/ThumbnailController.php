<?php

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
