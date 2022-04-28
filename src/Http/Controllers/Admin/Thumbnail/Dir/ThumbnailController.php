<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Thumbnail\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Http\Clients\Thumbnail\Client;
use N1ebieski\IDir\Http\Controllers\Admin\Thumbnail\Dir\Polymorphic;

class ThumbnailController implements Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param Client $client
     * @return JsonResponse
     */
    public function reload(Dir $dir, Client $client): JsonResponse
    {
        $client->patch(Config::get('idir.dir.thumbnail.api.reload_url'), [$dir->url->getValue()]);

        sleep(10);

        Cache::forget("dir.thumbnailUrl.{$dir->slug}");

        return Response::json([
            'success' => '',
            'thumbnail_url' => $dir->thumbnail_url
        ]);
    }
}
