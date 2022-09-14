<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

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
        $load->getRating()->makeService()->createOrUpdateOrDelete(
            $request->safe()->merge([
                'morph' => $dir,
                'user' => $request->user()
            ])->toArray()
        );

        return Response::json([
            'sum_rating' => $dir->load('ratings')->sum_rating
        ]);
    }
}
