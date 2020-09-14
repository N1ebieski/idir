<?php

use Illuminate\Support\Facades\Route;

Route::get('stats/dir/{dir}/click', 'Stat\Dir\StatController@click')
    ->name('stat.dir.click')
    ->where('dir', '[0-9]+');
