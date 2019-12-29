<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'dirs/index', 'DirController@index')
    ->name('dir.index')
    ->middleware('permission:index dirs');

Route::get('dirs/{dir}/edit', 'DirController@edit')
    ->middleware('permission:edit dirs')
    ->name('dir.edit')
    ->where('dir', '[0-9]+');
Route::put('dirs/{dir}', 'DirController@update')
    ->name('dir.update')
    ->middleware('permission:edit dirs')
    ->where('dir', '[0-9]+');

// Full edit
Route::get('dirs/{dir}/edit/full/1', 'DirController@editFull1')
    ->name('dir.edit_full_1')
    ->middleware('permission:edit dirs')
    ->where('dir', '[0-9]+');

Route::get('dirs/{dir}/group/{group}/edit/full/2', 'DirController@editFull2')
    ->middleware('permission:edit dirs')
    ->name('dir.edit_full_2')
    ->where('group', '[0-9]+')
    ->where('dir', '[0-9]+');
Route::put('dirs/{dir}/group/{group}/2', 'DirController@updateFull2')
    ->middleware('permission:edit dirs')
    ->name('dir.update_full_2')
    ->where('group', '[0-9]+')
    ->where('dir', '[0-9]+');

Route::get('dirs/{dir}/group/{group}/edit/3', 'DirController@editFull3')
    ->middleware('permission:edit dirs')
    ->name('dir.edit_full_3')
    ->where('group', '[0-9]+')
    ->where('dir', '[0-9]+');
Route::put('dirs/{dir}/group/{group}/3', 'DirController@updateFull3')
    ->middleware('permission:edit dirs')
    ->name('dir.update_full_3')
    ->where('group', '[0-9]+')
    ->where('dir', '[0-9]+');

Route::patch('dirs/{dir}', 'DirController@updateStatus')
    ->name('dir.update_status')
    ->middleware('permission:status dirs')
    ->where('dir', '[0-9]+');

Route::delete('dirs/{dir}', 'DirController@destroy')
    ->middleware('permission:destroy dirs')
    ->name('dir.destroy')
    ->where('dir', '[0-9]+');
Route::delete('dirs', 'DirController@destroyGlobal')
    ->name('dir.destroy_global')
    ->middleware('permission:destroy dirs');

Route::get('dirs/create/1', 'DirController@create1')
    ->name('dir.create_1')
    ->middleware('permission:create dirs');

Route::get('dirs/group/{group}/create/2', 'DirController@create2')
    ->where('group', '[0-9]+')
    ->name('dir.create_2')
    ->middleware('permission:create dirs');
Route::post('dirs/group/{group}/2', 'DirController@store2')
    ->where('group', '[0-9]+')
    ->middleware('permission:create dirs')
    ->name('dir.store_2');

Route::get('dirs/group/{group}/create/3', 'DirController@create3')
    ->where('group', '[0-9]+')
    ->middleware('permission:create dirs')
    ->name('dir.create_3');
Route::post('dirs/group/{group}/3', 'DirController@store3')
    ->where('group', '[0-9]+')
    ->middleware('permission:create dirs')
    ->name('dir.store_3');
