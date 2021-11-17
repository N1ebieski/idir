<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Thumbnail\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use GuzzleHttp\Client as GuzzleClient;

interface Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param GuzzleClient $guzzle
     * @return JsonResponse
     */
    public function reload(Dir $dir, GuzzleClient $guzzle): JsonResponse;
}
