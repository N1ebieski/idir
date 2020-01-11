<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Thumbnail\Dir;

use N1ebieski\IDir\Models\Dir;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\JsonResponse;

interface Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param GuzzleClient $guzzle
     * @return JsonResponse
     */
    public function reload(Dir $dir, GuzzleClient $guzzle) : JsonResponse;    
}