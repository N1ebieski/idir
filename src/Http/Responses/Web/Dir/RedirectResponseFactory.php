<?php

namespace N1ebieski\IDir\Http\Responses\Web\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\RedirectResponse;

interface RedirectResponseFactory
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @return RedirectResponse
     */
    public function makeResponse(Dir $dir): RedirectResponse;
}
