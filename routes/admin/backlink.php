<?php

use Illuminate\Support\Facades\Route;

Route::patch('backlink/{dirBacklink}', 'DirBacklinkController@delay')
    ->name('backlink.delay')
    ->middleware('permission:admin.dirs.status')
    ->where('dirBacklink', '[0-9]+');
