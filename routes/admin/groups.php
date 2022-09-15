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
use N1ebieski\IDir\Http\Controllers\Admin\GroupController;

Route::match(['post', 'get'], 'groups/index', [GroupController::class, 'index'])
    ->name('group.index')
    ->middleware('permission:admin.groups.view');

Route::get('groups/{group}/edit', [GroupController::class, 'edit'])
    ->name('group.edit')
    ->where('group', '[0-9]+')
    ->middleware('permission:admin.groups.edit');
Route::put('groups/{group}', [GroupController::class, 'update'])
    ->name('group.update')
    ->where('group', '[0-9]+')
    ->middleware('permission:admin.groups.edit');

Route::get('groups/{group}/edit/position', [GroupController::class, 'editPosition'])
    ->name('group.edit_position')
    ->where('group', '[0-9]+')
    ->middleware('permission:admin.groups.edit');
Route::patch('groups/{group}/position', [GroupController::class, 'updatePosition'])
    ->name('group.update_position')
    ->where('group', '[0-9]+')
    ->middleware('permission:admin.groups.edit');

Route::get('groups/create', [GroupController::class, 'create'])
    ->name('group.create')
    ->middleware('permission:admin.groups.create');
Route::post('groups', [GroupController::class, 'store'])
    ->name('group.store')
    ->middleware('permission:admin.groups.create');

Route::delete('groups/{group}', [GroupController::class, 'destroy'])
    ->name('group.destroy')
    ->where('group', '[0-9]+')
    ->middleware('permission:admin.groups.delete');
