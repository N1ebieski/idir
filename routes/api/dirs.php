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
use N1ebieski\IDir\Http\Controllers\Api\Dir\DirController;

Route::post('dirs/group/{group}', [DirController::class, 'store'])
    // ->middleware(['permission:api.dirs.create', 'ability:api.dirs.create'])
    ->where('group', '[0-9]+')
    ->name('dir.store');

Route::group(['middleware' => 'auth:sanctum', 'permission:api.access'], function () {
    Route::match(['post', 'get'], 'dirs/index', [DirController::class, 'index'])
        ->name('dir.index')
        ->middleware(['permission:api.dirs.view', 'ability:api.dirs.view']);

    Route::put('dirs/{dir}/group/{group}', [DirController::class, 'update'])
        ->name('dir.update')
        ->where('dir', '[0-9]+')
        ->where('group', '[0-9]+')
        ->middleware(['permission:api.dirs.edit', 'ability:api.dirs.edit', 'can:edit,dir']);

    Route::patch('dirs/{dir}/status', [DirController::class, 'updateStatus'])
        ->name('dir.update_status')
        ->where('dir', '[0-9]+')
        ->middleware(['permission:admin.dirs.status', 'permission:api.dirs.status', 'ability:api.dirs.status']);

    Route::delete('dirs/{dir}', [DirController::class, 'destroy'])
        ->name('dir.destroy')
        ->where('dir', '[0-9]+')
        ->middleware(['permission:api.dirs.delete', 'ability:api.dirs.delete', 'can:delete,dir']);
});
