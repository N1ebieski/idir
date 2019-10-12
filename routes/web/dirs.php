<?php

use Illuminate\Support\Facades\Route;

Route::get('dirs/{dir_cache}', 'DirController@show')
    ->name('dir.show')
    ->where('dir_cache', '[0-9A-Za-z,_-]+');

Route::group(['middleware' => 'auth'], function() {
    Route::get('dirs/create/group', 'DirController@createGroup')
        ->middleware(['icore.ban.user', 'icore.ban.ip'])
        ->name('dir.create_group');

    Route::get('dirs/group/{group_dir_available}/create/form', 'DirController@createForm')
        ->middleware(['icore.ban.user', 'icore.ban.ip'])
        ->where('group_dir_available', '[0-9]+')
        ->name('dir.create_form');
    Route::post('dirs/group/{group_dir_available}/form', 'DirController@storeForm')
        ->middleware(['icore.ban.user', 'icore.ban.ip'])
        ->where('group_dir_available', '[0-9]+')
        ->name('dir.store_form');

    Route::get('dirs/group/{group_dir_available}/create/summary', 'DirController@createSummary')
        ->middleware(['icore.ban.user', 'icore.ban.ip'])
        ->where('group_dir_available', '[0-9]+')
        ->name('dir.create_summary');
    Route::post('dirs/group/{group_dir_available}/summary', 'DirController@storeSummary')
        ->middleware(['icore.ban.user', 'icore.ban.ip'])    
        ->where('group_dir_available', '[0-9]+')
        ->name('dir.store_summary');
});
