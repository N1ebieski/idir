<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Web\Stat\Dir\StatController as DirStatController;

Route::get('stats/{stat_dir_cache}/dir/{dir_cache}', [DirStatController::class, 'click'])
    ->where('stat_dir_cache', 'click')
    ->where('dir_cache', '[0-9A-Za-z,_-]+')
    ->name('stat.dir.click');
