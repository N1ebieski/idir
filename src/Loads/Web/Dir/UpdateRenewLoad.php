<?php

namespace N1ebieski\IDir\Loads\Web\Dir;

use Illuminate\Http\Request;

class UpdateRenewLoad
{
    /**
     * [__construct description]
     * @param Request $request [description]
     */
    public function __construct(Request $request)
    {
        $request->route('dir')->load(['group', 'group.prices']);
    }
}
