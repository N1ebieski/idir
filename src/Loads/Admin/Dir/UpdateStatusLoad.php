<?php

namespace N1ebieski\IDir\Loads\Admin\Dir;

use Illuminate\Http\Request;

/**
 * [UpdateStatusLoad description]
 */
class UpdateStatusLoad
{
    /**
     * [__construct description]
     * @param Request $request [description]
     */
    public function __construct(Request $request)
    {
        $request->route('dir')->load([
            'group',
            'fields',
            'categories',
            'tags'
        ]);
    }
}
