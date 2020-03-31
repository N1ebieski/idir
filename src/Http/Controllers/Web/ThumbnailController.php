<?php

namespace N1ebieski\IDir\Http\Controllers\Web;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use N1ebieski\IDir\Utils\Thumbnail\ThumbnailUtil;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Http\Requests\Web\Thumbnail\ShowRequest;

class ThumbnailController extends Controller
{
    /**
     * Undocumented function
     *
     * @param ShowRequest $request
     * @return HttpResponse
     */
    public function show(ShowRequest $request) : HttpResponse
    {
        $thumbnail = App::make(ThumbnailUtil::class, ['url' => $request->input('url')]);

        return Response::make($thumbnail->generate(), 200, ['Content-Type' => 'image'])
            ->setMaxAge(Config::get('idir.dir.thumbnail.cache.days')*24*60*60)
            ->setLastModified(
                Carbon::createFromTimestamp($thumbnail->getLastModified())->toDateTime()
            )
            ->setPublic();
    }
}
