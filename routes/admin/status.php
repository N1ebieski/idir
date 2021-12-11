<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Admin\DirStatusController;

Route::patch('status/{dirStatus}', [DirStatusController::class, 'delay'])
    ->name('status.delay')
    ->middleware('permission:admin.dirs.status')
    ->where('dirStatus', '[0-9]+');
