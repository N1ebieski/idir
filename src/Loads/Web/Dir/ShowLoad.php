<?php

namespace N1ebieski\IDir\Loads\Web\Dir;

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Dir;

class ShowLoad
{
    /**
     * [__construct description]
     * @param Request $request [description]
     */
    public function __construct(Request $request)
    {
        /**
         * @var Dir
         */
        $dir = $request->route('dir_cache');

        $dir->setRelations($dir->makeCache()->rememberLoadAllPublicRels()->getRelations());
    }
}
