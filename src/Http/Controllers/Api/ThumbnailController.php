<?php

namespace N1ebieski\IDir\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use N1ebieski\IDir\Http\Requests\Web\Thumbnail\ShowRequest;
use N1ebieski\IDir\Utils\Thumbnail;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Http\Requests\Api\Thumbnail\ReloadRequest;

class ThumbnailController extends Controller
{
    public function reload(ReloadRequest $request) : JsonResponse
    {
        app(Thumbnail::class, ['url' => $request->input('url')])->reload();

        return response()->json([
            'success' => ''
        ]);
    }
}
