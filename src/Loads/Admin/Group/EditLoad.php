<?php

namespace N1ebieski\IDir\Loads\Admin\Group;

use Illuminate\Http\Request;

class EditLoad
{
    /**
     * [__construct description]
     * @param Request $request [description]
     */
    public function __construct(Request $request)
    {
        $request->route('group')->load(['prices', 'prices.codes']);
    }
}
