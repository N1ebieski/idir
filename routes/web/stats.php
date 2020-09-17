<?php

use Illuminate\Support\Facades\Route;

Route::get('stats/{stat_dir_cache}/dir/{dir_cache}', 'Stat\Dir\StatController@click')
    ->where('stat_dir_cache', 'click')
    ->where('dir_cache', '[0-9A-Za-z,_-]+')
    ->name('stat.dir.click');
