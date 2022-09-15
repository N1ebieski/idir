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
use N1ebieski\IDir\Http\Controllers\Admin\Field\FieldController;
use N1ebieski\IDir\Http\Controllers\Admin\Field\Group\FieldController as GroupFieldController;

Route::match(['post', 'get'], 'fields/group/index', [GroupFieldController::class, 'index'])
    ->name('field.group.index')
    ->middleware('permission:admin.fields.view');

Route::get('fields/{field}/group/edit', [GroupFieldController::class, 'edit'])
    ->name('field.group.edit')
    ->where('field', '[0-9]+')
    ->middleware('permission:admin.fields.edit');
Route::put('fields/{field}/group', [GroupFieldController::class, 'update'])
    ->name('field.group.update')
    ->where('field', '[0-9]+')
    ->middleware('permission:admin.fields.edit');

Route::get('fields/{field}/edit/position', [FieldController::class, 'editPosition'])
    ->name('field.edit_position')
    ->where('field', '[0-9]+')
    ->middleware('permission:admin.fields.edit');
Route::patch('fields/{field}/position', [FieldController::class, 'updatePosition'])
    ->name('field.update_position')
    ->where('field', '[0-9]+')
    ->middleware('permission:admin.fields.edit');

Route::get('fields/group/create', [GroupFieldController::class, 'create'])
    ->name('field.group.create')
    ->middleware('permission:admin.fields.create');
Route::post('fields/group', [GroupFieldController::class, 'store'])
    ->name('field.group.store')
    ->middleware('permission:admin.fields.create');

Route::delete('fields/{field}', [FieldController::class, 'destroy'])
    ->name('field.destroy')
    ->where('field', '[0-9]+')
    ->middleware('permission:admin.fields.delete');

Route::post('fields/gus', [FieldController::class, 'gus'])
    ->name('field.gus')
    ->where('field', '[0-9]+');
