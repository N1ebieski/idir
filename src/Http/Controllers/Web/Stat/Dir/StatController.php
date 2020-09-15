<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Stat\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Http\Controllers\Web\Stat\Dir\Polymorphic;
use N1ebieski\IDir\Models\Stat\Dir\Stat;

class StatController
{
    public function click(Stat $stat, Dir $dir) : JsonResponse
    {
        dd($dir->load('stats'));

        $dir->stats()
            ->syncWithoutDetaching([
                $stat->id => [
                    'value' => DB::raw("`value` + 1")
                ]
            ]);

        return Response::json(['success' => '']);
    }
}
