<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Thumbnail\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Http\Clients\Thumbnail\ThumbnailClient;
use N1ebieski\IDir\Http\Controllers\Admin\Thumbnail\Dir\Polymorphic;

class ThumbnailController implements Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param ThumbnailClient $client
     * @return JsonResponse
     */
    public function reload(Dir $dir, ThumbnailClient $client): JsonResponse
    {
        $client->reload(['url' => $dir->url->getValue()]);

        sleep(10);

        Cache::forget("dir.thumbnailUrl.{$dir->slug}");

        return Response::json([
            'thumbnail_url' => $dir->thumbnail_url
        ]);
    }
}
