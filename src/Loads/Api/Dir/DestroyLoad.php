<?php

namespace N1ebieski\IDir\Loads\Api\Dir;

use Illuminate\Http\Request;

class DestroyLoad
{
    /**
     * [__construct description]
     * @param Request $request [description]
     */
    public function __construct(Request $request)
    {
        $request->route('dir')->load(['group', 'group.privileges', 'user']);
    }
}