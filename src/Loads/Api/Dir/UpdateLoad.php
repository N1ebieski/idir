<?php

namespace N1ebieski\IDir\Loads\Api\Dir;

use Illuminate\Http\Request;

class UpdateLoad
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

        $request->route('dir')->load(['backlink', 'fields', 'categories']);
    }
}
