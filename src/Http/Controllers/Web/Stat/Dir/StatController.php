<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Stat\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Http\Controllers\Web\Stat\Dir\Polymorphic;

class StatController implements Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @return JsonResponse
     */
    public function click(Dir $dir) : JsonResponse
    {
        Log::info('działa ponownie + 1');

        return Response::json(['success' => '']);
    }
}
