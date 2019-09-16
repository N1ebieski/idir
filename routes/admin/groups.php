<?php

use Illuminate\Support\Facades\Route;

Route::get('groups/dir', 'Group\Dir\GroupController@index')
    ->name('group.dir.index')
    ->middleware('permission:index groups');

Route::get('groups/{group}/edit', 'Group\GroupController@edit')
    ->middleware(['permission:edit groups', 'can:editDefault,group'])
    ->name('group.edit')
    ->where('group', '[0-9]+');
Route::put('groups/{group}', 'Group\GroupController@update')
    ->middleware(['permission:edit groups', 'can:editDefault,group'])
    ->name('group.update')
    ->where('group', '[0-9]+');

Route::get('groups/{group}/edit/position', 'Group\GroupController@editPosition')
    ->middleware('permission:edit groups')
    ->name('group.edit_position')
    ->where('group', '[0-9]+');
Route::patch('groups/{group}/position', 'Group\GroupController@updatePosition')
    ->name('group.update_position')
    ->middleware('permission:edit groups')
    ->where('group', '[0-9]+');

Route::get('groups/dir/create', 'Group\Dir\GroupController@create')
    ->name('group.dir.create')
    ->middleware('permission:create groups');
Route::post('groups/dir', 'Group\Dir\GroupController@store')
    ->name('group.dir.store')
    ->middleware('permission:create groups');

Route::delete('groups/{group}', 'Group\GroupController@destroy')
    ->middleware(['permission:destroy groups', 'can:deleteDefault,group'])
    ->name('group.destroy')
    ->where('group', '[0-9]+');
