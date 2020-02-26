<?php

namespace N1ebieski\IDir\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use N1ebieski\IDir\Utils\ThumbnailUtil;
use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Http\Requests\Api\Thumbnail\ReloadRequest;

class ThumbnailController extends Controller
{
    /**
     * Undocumented function
     *
     * @param ReloadRequest $request
     * @return JsonResponse
     */
    public function reload(ReloadRequest $request) : JsonResponse
    {
        app(ThumbnailUtil::class, ['url' => $request->input('url')])->reload();

        return response()->json([
            'success' => ''
        ]);
    }
}
