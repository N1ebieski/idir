<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Thumbnail\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Http\Clients\Thumbnail\ReloadClient;
use N1ebieski\IDir\Http\Controllers\Admin\Thumbnail\Dir\Polymorphic;

class ThumbnailController implements Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param GuzzleClient $guzzle
     * @return JsonResponse
     */
    public function reload(Dir $dir, ReloadClient $reloadClient): JsonResponse
    {
        $reloadClient->request([$dir->url]);

        sleep(10);

        Cache::forget("dir.thumbnailUrl.{$dir->slug}");

        return Response::json([
            'success' => '',
            'thumbnail_url' => $dir->thumbnail_url
        ]);
    }
}
