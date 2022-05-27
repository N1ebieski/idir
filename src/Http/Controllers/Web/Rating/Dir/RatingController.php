<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Rating\Dir;

use Throwable;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Loads\Web\Rating\Dir\RateLoad;
use N1ebieski\IDir\Http\Requests\Web\Rating\Dir\RateRequest;
use N1ebieski\IDir\Http\Controllers\Web\Rating\Dir\Polymorphic;

class RatingController implements Polymorphic
{
    /**
     *
     * @param Dir $dir
     * @param RateLoad $load
     * @param RateRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function rate(Dir $dir, RateLoad $load, RateRequest $request): JsonResponse
    {
        $load->getRating()->makeService()->createOrUpdateOrDelete($request->only('rating'));

        return Response::json([
            'sum_rating' => $dir->load('ratings')->sum_rating
        ]);
    }
}
