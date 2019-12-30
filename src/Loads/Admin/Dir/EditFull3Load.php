<?php

namespace N1ebieski\IDir\Loads\Admin\Dir;

use Illuminate\Http\Request;

/**
 * [EditFull3Load description]
 */
class EditFull3Load
{
    /**
     * [__construct description]
     * @param Request $request [description]
     */
    public function __construct(Request $request)
    {
        $request->route('group')
            ->loadCount(['dirs', 'dirs_today'])
            ->load(['privileges', 'fields']);

        $request->route('dir')->load('backlink');
    }
}
