<?php

use Illuminate\Support\Facades\Route;

Route::get('stats/dir/{dir}/click', 'Stats\Dir\StatsController@click')
    ->name('stats.dir.click')
    ->where('dir', '[0-9]+');
