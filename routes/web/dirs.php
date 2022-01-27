<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Web\DirController;

Route::match(['post', 'get'], 'dirs/index', [DirController::class, 'index'])
    ->name('dir.index');

Route::match(['post', 'get'], 'dirs/search', [DirController::class, 'search'])
    ->name('dir.search');

Route::match(['post', 'get'], 'dirs/{dir_cache}', [DirController::class, 'show'])
    ->name('dir.show')
    ->where('dir_cache', '[0-9A-Za-z,_-]+');

Route::group(['middleware' => ['icore.ban.user', 'icore.ban.ip']], function () {
    Route::get('dirs/create/1', [DirController::class, 'create1'])
        ->name('dir.create_1');

    Route::get('dirs/group/{group}/create/2', [DirController::class, 'create2'])
        ->where('group', '[0-9]+')
        ->name('dir.create_2');
    Route::post('dirs/group/{group}/2', [DirController::class, 'store2'])
        ->where('group', '[0-9]+')
        ->name('dir.store_2');

    Route::get('dirs/group/{group}/create/3', [DirController::class, 'create3'])
        ->where('group', '[0-9]+')
        ->name('dir.create_3');
    Route::post('dirs/group/{group}/3', [DirController::class, 'store3'])
        ->where('group', '[0-9]+')
        ->name('dir.store_3');

    Route::group(['middleware' => ['auth']], function () {
        Route::delete('dirs/{dir}', [DirController::class, 'destroy'])
            ->middleware(['permission:web.dirs.delete', 'can:delete,dir'])
            ->name('dir.destroy')
            ->where('dir', '[0-9]+');

        Route::get('dirs/{dir}/edit/1', [DirController::class, 'edit1'])
            ->middleware(['permission:web.dirs.edit', 'can:edit,dir'])
            ->name('dir.edit_1')
            ->where('dir', '[0-9]+');

        Route::get('dirs/{dir}/group/{group}/edit/2', [DirController::class, 'edit2'])
            ->middleware(['permission:web.dirs.edit', 'can:edit,dir'])
            ->name('dir.edit_2')
            ->where('group', '[0-9]+')
            ->where('dir', '[0-9]+');
        Route::put('dirs/{dir}/group/{group}/2', [DirController::class, 'update2'])
            ->middleware(['permission:web.dirs.edit', 'can:edit,dir'])
            ->name('dir.update_2')
            ->where('group', '[0-9]+')
            ->where('dir', '[0-9]+');

        Route::get('dirs/{dir}/group/{group}/edit/3', [DirController::class, 'edit3'])
            ->middleware(['permission:web.dirs.edit', 'can:edit,dir'])
            ->name('dir.edit_3')
            ->where('group', '[0-9]+')
            ->where('dir', '[0-9]+');
        Route::put('dirs/{dir}/group/{group}/3', [DirController::class, 'update3'])
            ->middleware(['permission:web.dirs.edit', 'can:edit,dir'])
            ->name('dir.update_3')
            ->where('group', '[0-9]+')
            ->where('dir', '[0-9]+');

        Route::get('dirs/{dir}/edit/renew', [DirController::class, 'editRenew'])
            ->middleware(['permission:web.dirs.edit', 'can:edit,dir'])
            ->name('dir.edit_renew')
            ->where('dir', '[0-9]+');
        Route::patch('dirs/{dir}/renew', [DirController::class, 'updateRenew'])
            ->middleware(['permission:web.dirs.edit', 'can:edit,dir'])
            ->name('dir.update_renew')
            ->where('dir', '[0-9]+');
    });
});
