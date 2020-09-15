<?php

use Illuminate\Support\Facades\Route;

Route::get('stats/{stat_dir_cache}/dir/{dir}', 'Stat\Dir\StatController@click')
    ->where('stat_dir_cache', 'click')
    ->where('dir', '[0-9]+')
    ->name('stat.dir.click');
