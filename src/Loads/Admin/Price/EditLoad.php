<?php

namespace N1ebieski\IDir\Loads\Admin\Price;

use Illuminate\Http\Request;

class EditLoad
{
    /**
     * [__construct description]
     * @param Request $request [description]
     */
    public function __construct(Request $request)
    {
        $request->route('price')->load(['codes', 'group']);
    }
}
