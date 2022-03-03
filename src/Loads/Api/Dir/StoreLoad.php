<?php

namespace N1ebieski\IDir\Loads\Api\Dir;

use Illuminate\Http\Request;

class StoreLoad
{
    /**
     * [__construct description]
     * @param Request $request [description]
     */
    public function __construct(Request $request)
    {
        $request->route('group')
            ->loadCount(['dirs', 'dirsToday'])
            ->load(['privileges', 'fields']);
    }
}
