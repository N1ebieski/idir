<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Admin\DirBacklinkController;

Route::patch('backlink/{dirBacklink}', [DirBacklinkController::class, 'delay'])
    ->name('backlink.delay')
    ->middleware('permission:admin.dirs.status')
    ->where('dirBacklink', '[0-9]+');
