<?php

namespace N1ebieski\IDir\Http\Controllers\Web;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;

/**
 * [HomeController description]
 */
class HomeController
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @return HttpResponse
     */
    public function index(Dir $dir) : HttpResponse
    {
        return Response::view('idir::web.home.index', [
            'dirs' => $dir->makeCache()->rememberLatestForHome()
        ]);
    }
}
