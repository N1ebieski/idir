<?php

namespace N1ebieski\IDir\Http\Controllers\Web;

use Illuminate\Http\Request;

class DirController
{
    public function select()
    {
        return view('idir::web.dir.select');
    }
}
