<?php

use Illuminate\Support\Facades\Route;

Route::get('groups', 'GroupController@index')
    ->name('group.index')
    ->middleware('permission:index groups');

Route::get('groups/{group}/edit', 'GroupController@edit')
    ->middleware(['permission:edit groups', 'can:editDefault,group'])
    ->name('group.edit')
    ->where('group', '[0-9]+');
Route::put('groups/{group}', 'GroupController@update')
    ->middleware(['permission:edit groups', 'can:editDefault,group'])
    ->name('group.update')
    ->where('group', '[0-9]+');

Route::get('groups/{group}/edit/position', 'GroupController@editPosition')
    ->middleware('permission:edit groups')
    ->name('group.edit_position')
    ->where('group', '[0-9]+');
Route::patch('groups/{group}/position', 'GroupController@updatePosition')
    ->name('group.update_position')
    ->middleware('permission:edit groups')
    ->where('group', '[0-9]+');

Route::get('groups/create', 'GroupController@create')
    ->name('group.create')
    ->middleware('permission:create groups');
Route::post('groups', 'GroupController@store')
    ->name('group.store')
    ->middleware('permission:create groups');

Route::delete('groups/{group}', 'GroupController@destroy')
    ->middleware(['permission:destroy groups', 'can:deleteDefault,group'])
    ->name('group.destroy')
    ->where('group', '[0-9]+');
