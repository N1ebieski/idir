<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Rating\Dir;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Rating\Dir\Rating;
use N1ebieski\IDir\Http\Requests\Web\Rating\Dir\RateRequest;
use Illuminate\Http\JsonResponse;

/**
 * [interface description]
 * @var [type]
 */
interface Polymorphic
{
    /**
    /**
     * Undocumented function
     *
     * @param Rating $rating
     * @param Dir $dir
     * @param RateRequest $request
     * @return JsonResponse
     */
    public function rate(Rating $rating, Dir $dir, RateRequest $request) : JsonResponse;
}
