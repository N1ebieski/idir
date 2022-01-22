<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Api\Dir\DirController;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::put('dirs/{dir}', [DirController::class, 'update'])
        ->middleware(['permission:api.dirs.edit', 'can:edit,dir'])
        ->name('dir.update')
        ->where('dir', '[0-9]+');
});
