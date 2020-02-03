<?php

namespace N1ebieski\IDir\Loads\Admin\Dir;

use Illuminate\Http\Request;

/**
 * [EditFull1Load description]
 */
class EditFull1Load
{
    /**
     * [__construct description]
     * @param Request $request [description]
     */
    public function __construct(Request $request)
    {
        $request->route('dir')->load([
            'fields', 
            'regions',
            'categories', 
            'group', 
            'group.prices'
        ]);
    }
}
