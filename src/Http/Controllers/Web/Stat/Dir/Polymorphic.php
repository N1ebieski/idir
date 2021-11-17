<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Stat\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Models\Stat\Dir\Stat;
use N1ebieski\IDir\Http\Requests\Web\Stat\Dir\ClickRequest;

interface Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Stat $stat
     * @param Dir $dir
     * @param ClickRequest $request
     * @return JsonResponse
     */
    public function click(Stat $stat, Dir $dir, ClickRequest $request): JsonResponse;
}
