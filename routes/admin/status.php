<?php

use Illuminate\Support\Facades\Route;

Route::patch('status/{dirStatus}', 'DirStatusController@delay')
    ->name('status.delay')
    ->middleware('permission:admin.dirs.status')
    ->where('dirStatus', '[0-9]+');
