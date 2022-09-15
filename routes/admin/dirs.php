<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Admin\DirController;

Route::match(['post', 'get'], 'dirs/index', [DirController::class, 'index'])
    ->name('dir.index')
    ->middleware('permission:admin.dirs.view');

Route::get('dirs/{dir}/edit', [DirController::class, 'edit'])
    ->name('dir.edit')
    ->where('dir', '[0-9]+')
    ->middleware('permission:admin.dirs.edit');
Route::put('dirs/{dir}', [DirController::class, 'update'])
    ->name('dir.update')
    ->where('dir', '[0-9]+')
    ->middleware('permission:admin.dirs.edit');

Route::get('dirs/{dir}/edit/full/1', [DirController::class, 'editFull1'])
    ->name('dir.edit_full_1')
    ->where('dir', '[0-9]+')
    ->middleware('permission:admin.dirs.edit');

Route::get('dirs/{dir}/group/{group}/edit/full/2', [DirController::class, 'editFull2'])
    ->name('dir.edit_full_2')
    ->where('group', '[0-9]+')
    ->where('dir', '[0-9]+')
    ->middleware('permission:admin.dirs.edit');
Route::put('dirs/{dir}/group/{group}/2', [DirController::class, 'updateFull2'])
    ->name('dir.update_full_2')
    ->where('group', '[0-9]+')
    ->where('dir', '[0-9]+')
    ->middleware('permission:admin.dirs.edit');

Route::get('dirs/{dir}/group/{group}/edit/3', [DirController::class, 'editFull3'])
    ->name('dir.edit_full_3')
    ->where('group', '[0-9]+')
    ->where('dir', '[0-9]+')
    ->middleware('permission:admin.dirs.edit');
Route::put('dirs/{dir}/group/{group}/3', [DirController::class, 'updateFull3'])
    ->name('dir.update_full_3')
    ->where('group', '[0-9]+')
    ->where('dir', '[0-9]+')
    ->middleware('permission:admin.dirs.edit');

Route::patch('dirs/{dir}/status', [DirController::class, 'updateStatus'])
    ->name('dir.update_status')
    ->where('dir', '[0-9]+')
    ->middleware('permission:admin.dirs.status');

Route::patch('dirs/{dir}/thumbnail', [DirController::class, 'updateThumbnail'])
    ->name('dir.update_thumbnail')
    ->where('dir', '[0-9]+')
    ->middleware('permission:admin.dirs.view');

Route::delete('dirs/{dir}', [DirController::class, 'destroy'])
    ->name('dir.destroy')
    ->where('dir', '[0-9]+')
    ->middleware('permission:admin.dirs.delete');
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
    ->name('dir.store_2')
    ->middleware('permission:admin.dirs.create');

Route::get('dirs/group/{group}/create/3', [DirController::class, 'create3'])
    ->where('group', '[0-9]+')
    ->name('dir.create_3')
    ->middleware('permission:admin.dirs.create');
Route::post('dirs/group/{group}/3', [DirController::class, 'store3'])
    ->where('group', '[0-9]+')
    ->name('dir.store_3')
    ->middleware('permission:admin.dirs.create');
