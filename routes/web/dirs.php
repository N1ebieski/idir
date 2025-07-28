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
use N1ebieski\IDir\Http\Controllers\Web\DirController;

Route::match(['post', 'get'], 'dirs/index', [DirController::class, 'index'])
    ->name('dir.index');

Route::match(['post', 'get'], 'dirs/search', [DirController::class, 'search'])
    ->name('dir.search');

Route::match(['post', 'get'], 'dirs/{dir_cache}', [DirController::class, 'show'])
    ->name('dir.show')
    ->where('dir_cache', '[0-9A-Za-z,_-]+');

Route::post('dirs/group/{group}/generate-content', [DirController::class, 'generateContent'])
    ->where('group', '[0-9]+')
    ->name('dir.generate_content');

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
            ->name('dir.destroy')
            ->where('dir', '[0-9]+')
            ->middleware(['permission:web.dirs.delete', 'can:delete,dir']);

        Route::get('dirs/{dir}/edit/1', [DirController::class, 'edit1'])
            ->name('dir.edit_1')
            ->where('dir', '[0-9]+')
            ->middleware(['permission:web.dirs.edit', 'can:edit,dir']);

        Route::get('dirs/{dir}/group/{group}/edit/2', [DirController::class, 'edit2'])
            ->name('dir.edit_2')
            ->where('group', '[0-9]+')
            ->where('dir', '[0-9]+')
            ->middleware(['permission:web.dirs.edit', 'can:edit,dir']);
        Route::put('dirs/{dir}/group/{group}/2', [DirController::class, 'update2'])
            ->name('dir.update_2')
            ->where('group', '[0-9]+')
            ->where('dir', '[0-9]+')
            ->middleware(['permission:web.dirs.edit', 'can:edit,dir']);

        Route::get('dirs/{dir}/group/{group}/edit/3', [DirController::class, 'edit3'])
            ->name('dir.edit_3')
            ->where('group', '[0-9]+')
            ->where('dir', '[0-9]+')
            ->middleware(['permission:web.dirs.edit', 'can:edit,dir']);
        Route::put('dirs/{dir}/group/{group}/3', [DirController::class, 'update3'])
            ->name('dir.update_3')
            ->where('group', '[0-9]+')
            ->where('dir', '[0-9]+')
            ->middleware(['permission:web.dirs.edit', 'can:edit,dir']);

        Route::get('dirs/{dir}/edit/renew', [DirController::class, 'editRenew'])
            ->name('dir.edit_renew')
            ->where('dir', '[0-9]+')
            ->middleware(['permission:web.dirs.edit', 'can:edit,dir']);
        Route::patch('dirs/{dir}/renew', [DirController::class, 'updateRenew'])
            ->name('dir.update_renew')
            ->where('dir', '[0-9]+')
            ->middleware(['permission:web.dirs.edit', 'can:edit,dir']);
    });
});
