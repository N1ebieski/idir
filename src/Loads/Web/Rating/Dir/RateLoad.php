<?php

namespace N1ebieski\IDir\Loads\Web\Rating\Dir;

use Illuminate\Http\Request;

/**
 * [RateLoad description]
 */
class RateLoad
{
    /**
     * [__construct description]
     * @param Request $request [description]
     */
    public function __construct(Request $request)
    {
        $request->route('dir')->load('ratings');
    }
}
