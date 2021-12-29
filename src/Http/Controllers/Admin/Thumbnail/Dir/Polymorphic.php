<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Thumbnail\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Http\Clients\Thumbnail\ReloadClient;

interface Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param ReloadClient $reloadClient
     * @return JsonResponse
     */
    public function reload(Dir $dir, ReloadClient $reloadClient): JsonResponse;
}
