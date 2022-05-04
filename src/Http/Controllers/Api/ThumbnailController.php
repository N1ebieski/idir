<?php

namespace N1ebieski\IDir\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use N1ebieski\IDir\Utils\Thumbnail\ThumbnailUtil;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Http\Requests\Api\Thumbnail\ReloadRequest;

class ThumbnailController extends Controller
{
    /**
     * @hideFromAPIDocumentation
     *
     * @param ReloadRequest $request
     * @return JsonResponse
     */
    public function reload(ReloadRequest $request, ThumbnailUtil $thumbnailUtil): JsonResponse
    {
        $thumbnailUtil->make($request->input('url'))->reload();

        return Response::json([]);
    }
}
