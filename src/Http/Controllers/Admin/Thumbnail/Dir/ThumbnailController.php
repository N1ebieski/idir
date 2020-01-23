<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Thumbnail\Dir;

use N1ebieski\IDir\Models\Dir;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\JsonResponse;
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
                config('idir.dir.thumbnail.api.reload_url') . $dir->url, 
                [
                    'headers' => ['Authorization' => config('idir.dir.thumbnail.key')]
                ]
            );
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \N1ebieski\IDir\Exceptions\Thumbnail\Exception(
                $e->getMessage(), $e->getCode()
            );
        }

        cache()->forget("dir.thumbnailUrl.{$dir->slug}");

        return response()->json([
            'success' => '',
            'thumbnail_url' => $dir->thumbnail_url
        ]);        
    }    
}