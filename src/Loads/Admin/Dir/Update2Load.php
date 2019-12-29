<?php

namespace N1ebieski\IDir\Loads\Web\Dir;

use Illuminate\Http\Request;

/**
 * [Update2Load description]
 */
class Update2Load
{
    /**
     * [__construct description]
     * @param Request $request [description]
     */
    public function __construct(Request $request)
    {
        $request->route('group')
            ->loadCount(['dirs', 'dirs_today'])
            ->load('privileges')
            ->loadPublicFields();
    }
}
