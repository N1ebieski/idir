<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Stats\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Http\Controllers\Web\Stats\Dir\Polymorphic;

class StatsController implements Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @return JsonResponse
     */
    public function click(Dir $dir) : JsonResponse
    {
        Log::info('dziala + 1');

        return Response::json(['success' => '']);
    }
}
