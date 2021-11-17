<?php

namespace N1ebieski\IDir\Loads\Web\Dir;

use Illuminate\Http\Request;

class ShowLoad
{
    /**
     * [__construct description]
     * @param Request $request [description]
     */
    public function __construct(Request $request)
    {
        $request->route('dir_cache')->setRelations(
            $request->route('dir_cache')->makeCache()
                ->rememberLoadAllPublicRels()->getRelations()
        );
    }
}
