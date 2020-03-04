<?php

namespace N1ebieski\IDir\Loads\Admin\Dir;

use Illuminate\Http\Request;

/**
 * [UpdateFull3Load description]
 */
class UpdateFull3Load
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

        $request->route('dir')->load(['backlink', 'fields']);
    }
}
