<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Admin\DirController;

Route::match(['get', 'post'], 'dirs/index', [DirController::class, 'index'])
    ->name('dir.index')
    ->middleware('permission:admin.dirs.view');

Route::get('dirs/{dir}/edit', [DirController::class, 'edit'])
    ->middleware('permission:admin.dirs.edit')
    ->name('dir.edit')
    ->where('dir', '[0-9]+');
Route::put('dirs/{dir}', [DirController::class, 'update'])
    ->name('dir.update')
    ->middleware('permission:admin.dirs.edit')
    ->where('dir', '[0-9]+');

Route::get('dirs/{dir}/edit/full/1', [DirController::class, 'editFull1'])
    ->name('dir.edit_full_1')
    ->middleware('permission:admin.dirs.edit')
    ->where('dir', '[0-9]+');

Route::get('dirs/{dir}/group/{group}/edit/full/2', [DirController::class, 'editFull2'])
    ->middleware('permission:admin.dirs.edit')
    ->name('dir.edit_full_2')
    ->where('group', '[0-9]+')
    ->where('dir', '[0-9]+');
Route::put('dirs/{dir}/group/{group}/2', [DirController::class, 'updateFull2'])
    ->middleware('permission:admin.dirs.edit')
    ->name('dir.update_full_2')
    ->where('group', '[0-9]+')
    ->where('dir', '[0-9]+');

Route::get('dirs/{dir}/group/{group}/edit/3', [DirController::class, 'editFull3'])
    ->middleware('permission:admin.dirs.edit')
    ->name('dir.edit_full_3')
    ->where('group', '[0-9]+')
    ->where('dir', '[0-9]+');
Route::put('dirs/{dir}/group/{group}/3', [DirController::class, 'updateFull3'])
    ->middleware('permission:admin.dirs.edit')
    ->name('dir.update_full_3')
    ->where('group', '[0-9]+')
    ->where('dir', '[0-9]+');

Route::patch('dirs/{dir}', [DirController::class, 'updateStatus'])
    ->name('dir.update_status')
    ->middleware('permission:admin.dirs.status')
    ->where('dir', '[0-9]+');

Route::patch('dirs/{dir}/thumbnail', [DirController::class, 'updateThumbnail'])
    ->name('dir.update_thumbnail')
    ->middleware('permission:admin.dirs.view')
    ->where('dir', '[0-9]+');

Route::delete('dirs/{dir}', [DirController::class, 'destroy'])
    ->middleware('permission:admin.dirs.delete')
    ->name('dir.destroy')
    ->where('dir', '[0-9]+');
Route::delete('dirs', [DirController::class, 'destroyGlobal'])
    ->name('dir.destroy_global')
    ->middleware('permission:admin.dirs.delete');

Route::get('dirs/create/1', [DirController::class, 'create1'])
    ->name('dir.create_1')
    ->middleware('permission:admin.dirs.create');

Route::get('dirs/group/{group}/create/2', [DirController::class, 'create2'])
    ->where('group', '[0-9]+')
    ->name('dir.create_2')
    ->middleware('permission:admin.dirs.create');
Route::post('dirs/group/{group}/2', [DirController::class, 'store2'])
    ->where('group', '[0-9]+')
    ->middleware('permission:admin.dirs.create')
    ->name('dir.store_2');

Route::get('dirs/group/{group}/create/3', [DirController::class, 'create3'])
    ->where('group', '[0-9]+')
    ->middleware('permission:admin.dirs.create')
    ->name('dir.create_3');
Route::post('dirs/group/{group}/3', [DirController::class, 'store3'])
    ->where('group', '[0-9]+')
    ->middleware('permission:admin.dirs.create')
    ->name('dir.store_3');
