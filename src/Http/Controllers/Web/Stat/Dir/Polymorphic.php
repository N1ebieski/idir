<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Stat\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Models\Stat\Dir\Stat;

interface Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Stat $stat
     * @param Dir $dir
     * @return JsonResponse
     */
    public function click(Stat $stat, Dir $dir) : JsonResponse;
}
