<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Rating\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Loads\Web\Rating\Dir\RateLoad;
use N1ebieski\IDir\Http\Requests\Web\Rating\Dir\RateRequest;

interface Polymorphic
{
    /**
     *
     * @param Dir $dir
     * @param RateLoad $load
     * @param RateRequest $request
     * @return JsonResponse
     */
    public function rate(Dir $dir, RateLoad $load, RateRequest $request): JsonResponse;
}
