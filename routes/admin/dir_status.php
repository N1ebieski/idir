<?php

use Illuminate\Support\Facades\Route;

Route::patch('dir-status/{dirStatus}', 'DirStatusController@delay')
    ->name('dir_status.delay')
    ->middleware('permission:admin.dirs.status')
    ->where('dirStatus', '[0-9]+');
