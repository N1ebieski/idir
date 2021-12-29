<?php

namespace N1ebieski\IDir\Http\Controllers\Admin;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Collection as Collect;
use N1ebieski\IDir\Http\Clients\Intelekt\Client;
use N1ebieski\IDir\Http\Responses\Data\Dir\Chart\StatusData as DirStatusData;
use N1ebieski\IDir\Http\Responses\Data\Dir\Chart\TimelineData as DirTimelineData;

class HomeController
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param Client $client
     * @return HttpResponse
     */
    public function index(Dir $dir, Client $client): HttpResponse
    {
        try {
            $posts = Collect::make($client->post('/api/posts/index', [
                'filter' => [
                    'status' => 1,
                    'orderby' => 'created_at|desc',
                    'search' => 'idir'
                ]
            ])->data);
        } catch (\N1ebieski\ICore\Exceptions\Client\TransferException $e) {
            $posts = null;
        }

        return Response::view('idir::admin.home.index', [
            'posts' => $posts,
            'countDirsByStatus' => App::make(DirStatusData::class, [
                'collection' => $dir->makeRepo()->countByStatus()
            ])->toArray(),
            'countDirsByDateAndGroup' => App::make(DirTimelineData::class, [
                'collection' => $dir->makeRepo()->countByDateAndGroup()
            ])->toArray()
        ]);
    }
}
