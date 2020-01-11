<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Payment\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;

interface Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @return JsonResponse
     */
    public function showLogs(Dir $dir) : JsonResponse;
}