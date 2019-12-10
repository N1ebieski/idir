<?php

namespace N1ebieski\IDir\Http\Controllers\Web;

use Illuminate\View\View;
use N1ebieski\IDir\Models\Dir;

/**
 * [ProfileController description]
 */
class ProfileController
{
    /**
     * [editDir description]
     * @param  Dir  $dir [description]
     * @return View      [description]
     */
    public function editDir(Dir $dir) : View
    {
        return view('idir::web.profile.edit_dir', [
            'dirs' => $dir->makeRepo()->paginateByUser(auth()->user()->id)
        ]);
    }
}
