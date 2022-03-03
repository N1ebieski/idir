<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Api\Dir\DirController;

Route::post('dirs/group/{group}', [DirController::class, 'store'])
    // ->middleware(['permission:api.dirs.create', 'ability:api.dirs.create'])
    ->where('group', '[0-9]+')
    ->name('dir.store');


Route::group(['middleware' => 'auth:sanctum', 'permission:api.access'], function () {
    Route::put('dirs/{dir}', [DirController::class, 'update'])
        ->middleware(['permission:api.dirs.edit', 'can:edit,dir'])
        ->name('dir.update')
        ->where('dir', '[0-9]+');
});
