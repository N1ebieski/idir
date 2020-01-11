<?php

namespace N1ebieski\IDir\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use N1ebieski\IDir\Http\Requests\Web\Thumbnail\ShowRequest;
use N1ebieski\IDir\Utils\Thumbnail;
use Carbon\Carbon;

class ThumbnailController extends Controller
{
    /**
     * Undocumented function
     *
     * @param ShowRequest $request
     * @return void
     */
    public function show(ShowRequest $request)
    {
        $thumbnail = app()->make(Thumbnail::class, ['url' => $request->input('url')]);

        return response()->make($thumbnail->generate(), 200, ['Content-Type' => 'image'])
            ->setMaxAge(config('idir.dir.thumbnail.cache.days')*24*60*60)
            ->setPublic();
    }
}
