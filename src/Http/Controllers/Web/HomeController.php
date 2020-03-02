<?php

namespace N1ebieski\IDir\Http\Controllers\Web;

use Illuminate\Support\Facades\View;
use N1ebieski\IDir\Models\Dir;

/**
 * [HomeController description]
 */
class HomeController
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Dir $dir)
    {
        return View::make('idir::web.home.index', [
            'dirs' => $dir->makeCache()->rememberLatestForHome()
        ]);
    }
}
