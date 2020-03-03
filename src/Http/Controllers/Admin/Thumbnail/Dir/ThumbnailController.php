<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Thumbnail\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
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
    public function reload(Dir $dir, GuzzleClient $guzzle) : JsonResponse
    {
        try {
            $guzzle->request(
                'PATCH',
                Config::get('idir.dir.thumbnail.api.reload_url') . $dir->url,
                [
                    'headers' => ['Authorization' => Config::get('idir.dir.thumbnail.key')]
                ]
            );
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \N1ebieski\IDir\Exceptions\Thumbnail\Exception(
                $e->getMessage(),
                $e->getCode()
            );
        }

        Cache::forget("dir.thumbnailUrl.{$dir->slug}");

        return Response::json([
            'success' => '',
            'thumbnail_url' => $dir->thumbnail_url
        ]);
    }
}
