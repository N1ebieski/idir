<?php

namespace N1ebieski\IDir\Http\Controllers\Web;

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Dir;

/**
 * [HomeController description]
 */
class HomeController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Dir $dir)
    {
        return view('idir::web.home.index', [
            'dirs' => $dir->makeCache()->rememberLatest()
        ]);
    }
}
