<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Api\Dir\DirController;

Route::post('dirs/group/{group}', [DirController::class, 'store'])
    // ->middleware(['permission:api.dirs.create', 'ability:api.dirs.create'])
    ->where('group', '[0-9]+')
    ->name('dir.store');

Route::group(['middleware' => 'auth:sanctum', 'permission:api.access'], function () {
    Route::match(['post', 'get'], 'dirs/index', [DirController::class, 'index'])
        ->middleware(['permission:api.dirs.ciew', 'ability:api.dirs.ciew'])
        ->name('dir.index');

    Route::put('dirs/{dir}/group/{group}', [DirController::class, 'update'])
        ->middleware(['permission:api.dirs.edit', 'ability:api.dirs.edit', 'can:edit,dir'])
        ->name('dir.update')
        ->where('dir', '[0-9]+')
        ->where('group', '[0-9]+');

    Route::patch('dirs/{dir}', [DirController::class, 'updateStatus'])
        ->name('dir.update_status')
        ->middleware(['permission:admin.dirs.status', 'permission:api.dirs.status', 'ability:api.dirs.status'])
        ->where('dir', '[0-9]+');

    Route::delete('dirs/{dir}', [DirController::class, 'destroy'])
        ->middleware(['permission:api.dirs.delete', 'ability:api.dirs.delete', 'can:delete,dir'])
        ->name('dir.destroy')
        ->where('dir', '[0-9]+');
});
