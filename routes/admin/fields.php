<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Admin\Field\FieldController;
use N1ebieski\IDir\Http\Controllers\Admin\Field\Group\FieldController as GroupFieldController;

Route::match(['get', 'post'], 'fields/group/index', [GroupFieldController::class, 'index'])
    ->name('field.group.index')
    ->middleware('permission:admin.fields.view');

Route::get('fields/{field}/group/edit', [GroupFieldController::class, 'edit'])
    ->middleware('permission:admin.fields.edit')
    ->name('field.group.edit')
    ->where('field', '[0-9]+');
Route::put('fields/{field}/group', [GroupFieldController::class, 'update'])
    ->middleware('permission:admin.fields.edit')
    ->name('field.group.update')
    ->where('field', '[0-9]+');

Route::get('fields/{field}/edit/position', [FieldController::class, 'editPosition'])
    ->middleware('permission:admin.fields.edit')
    ->name('field.edit_position')
    ->where('field', '[0-9]+');
Route::patch('fields/{field}/position', [FieldController::class, 'updatePosition'])
    ->name('field.update_position')
    ->middleware('permission:admin.fields.edit')
    ->where('field', '[0-9]+');

Route::get('fields/group/create', [GroupFieldController::class, 'create'])
    ->name('field.group.create')
    ->middleware('permission:admin.fields.create');
Route::post('fields/group', [GroupFieldController::class, 'store'])
    ->name('field.group.store')
    ->middleware('permission:admin.fields.create');

Route::delete('fields/{field}', [FieldController::class, 'destroy'])
    ->middleware('permission:admin.fields.delete')
    ->name('field.destroy')
    ->where('field', '[0-9]+');

Route::post('fields/gus', [FieldController::class, 'gus'])
    ->name('field.gus')
    ->where('field', '[0-9]+');
