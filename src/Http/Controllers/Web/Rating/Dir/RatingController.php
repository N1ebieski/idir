<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Rating\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Models\Rating\Dir\Rating;
use N1ebieski\IDir\Http\Requests\Web\Rating\Dir\RateRequest;
use N1ebieski\IDir\Http\Controllers\Web\Rating\Dir\Polymorphic;

/**
 * [RatingController description]
 */
class RatingController implements Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Rating $rating
     * @param Dir $dir
     * @param RateRequest $request
     * @return JsonResponse
     */
    public function rate(Rating $rating, Dir $dir, RateRequest $request) : JsonResponse
    {
        $rating->setMorph($dir)->makeService()->createOrUpdateOrDelete($request->only('rating'));

        return Response::json([
            'success' => '',
            'sum_rating' => $dir->load('ratings')->sum_rating
        ]);
    }
}
