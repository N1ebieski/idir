<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'fields/group/index', 'Field\Group\FieldController@index')
    ->name('field.group.index')
    ->middleware('permission:index fields');

Route::get('fields/{field}/group/edit', 'Field\Group\FieldController@edit')
    ->middleware('permission:edit fields')
    ->name('field.group.edit')
    ->where('field', '[0-9]+');
Route::put('fields/{field}/group', 'Field\Group\FieldController@update')
    ->middleware('permission:edit fields')
    ->name('field.group.update')
    ->where('field', '[0-9]+');

Route::get('fields/{field}/edit/position', 'Field\FieldController@editPosition')
    ->middleware('permission:edit fields')
    ->name('field.edit_position')
    ->where('field', '[0-9]+');
Route::patch('fields/{field}/position', 'Field\FieldController@updatePosition')
    ->name('field.update_position')
    ->middleware('permission:edit fields')
    ->where('field', '[0-9]+');

Route::get('fields/group/create', 'Field\Group\FieldController@create')
    ->name('field.group.create')
    ->middleware('permission:create fields');
Route::post('fields/group', 'Field\Group\FieldController@store')
    ->name('field.group.store')
    ->middleware('permission:create fields');

Route::delete('fields/{field}', 'Field\FieldController@destroy')
    ->middleware('permission:destroy fields')
    ->name('field.destroy')
    ->where('field', '[0-9]+');
