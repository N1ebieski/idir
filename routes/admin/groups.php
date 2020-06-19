<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'groups/index', 'GroupController@index')
    ->name('group.index')
    ->middleware('permission:admin.groups.view');

Route::get('groups/{group}/edit', 'GroupController@edit')
    ->middleware('permission:admin.groups.edit')
    ->name('group.edit')
    ->where('group', '[0-9]+');
Route::put('groups/{group}', 'GroupController@update')
    ->middleware('permission:admin.groups.edit')
    ->name('group.update')
    ->where('group', '[0-9]+');

Route::get('groups/{group}/edit/position', 'GroupController@editPosition')
    ->middleware('permission:admin.groups.edit')
    ->name('group.edit_position')
    ->where('group', '[0-9]+');
Route::patch('groups/{group}/position', 'GroupController@updatePosition')
    ->name('group.update_position')
    ->middleware('permission:admin.groups.edit')
    ->where('group', '[0-9]+');

Route::get('groups/create', 'GroupController@create')
    ->name('group.create')
    ->middleware('permission:admin.groups.create');
Route::post('groups', 'GroupController@store')
    ->name('group.store')
    ->middleware('permission:admin.groups.create');

Route::delete('groups/{group}', 'GroupController@destroy')
    ->middleware('permission:admin.groups.delete')
    ->name('group.destroy')
    ->where('group', '[0-9]+');
