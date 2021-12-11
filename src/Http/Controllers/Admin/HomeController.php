<?php

namespace N1ebieski\IDir\Http\Controllers\Admin;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Http\Responses\Data\Dir\Chart\StatusData as DirStatusData;
use N1ebieski\IDir\Http\Responses\Data\Dir\Chart\TimelineData as DirTimelineData;

class HomeController
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @return HttpResponse
     */
    public function index(Dir $dir): HttpResponse
    {
        return Response::view('idir::admin.home.index', [
            'countDirsByStatus' => App::make(DirStatusData::class, [
                'collection' => $dir->makeRepo()->countByStatus()
            ])->toArray(),
            'countDirsByDateAndGroup' => App::make(DirTimelineData::class, [
                'collection' => $dir->makeRepo()->countByDateAndGroup()
            ])->toArray()
        ]);
    }
}
