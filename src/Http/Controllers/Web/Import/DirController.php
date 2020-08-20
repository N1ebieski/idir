<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Import;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;

class DirController
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @return RedirectResponse
     */
    public function show(Dir $dir) : RedirectResponse
    {
        return Response::redirectToRoute(
            'web.dir.show', [$dir->slug],
            HttpResponse::HTTP_MOVED_PERMANENTLY
        );
    }
}
