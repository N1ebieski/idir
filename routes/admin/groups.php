<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Admin\GroupController;

Route::match(['get', 'post'], 'groups/index', [GroupController::class, 'index'])
    ->name('group.index')
    ->middleware('permission:admin.groups.view');

Route::get('groups/{group}/edit', [GroupController::class, 'edit'])
    ->middleware('permission:admin.groups.edit')
    ->name('group.edit')
    ->where('group', '[0-9]+');
Route::put('groups/{group}', [GroupController::class, 'update'])
    ->middleware('permission:admin.groups.edit')
    ->name('group.update')
    ->where('group', '[0-9]+');

Route::get('groups/{group}/edit/position', [GroupController::class, 'editPosition'])
    ->middleware('permission:admin.groups.edit')
    ->name('group.edit_position')
    ->where('group', '[0-9]+');
Route::patch('groups/{group}/position', [GroupController::class, 'updatePosition'])
    ->name('group.update_position')
    ->middleware('permission:admin.groups.edit')
    ->where('group', '[0-9]+');

Route::get('groups/create', [GroupController::class, 'create'])
    ->name('group.create')
    ->middleware('permission:admin.groups.create');
Route::post('groups', [GroupController::class, 'store'])
    ->name('group.store')
    ->middleware('permission:admin.groups.create');

Route::delete('groups/{group}', [GroupController::class, 'destroy'])
    ->middleware('permission:admin.groups.delete')
    ->name('group.destroy')
    ->where('group', '[0-9]+');
