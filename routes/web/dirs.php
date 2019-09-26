<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function() {
    Route::get('dirs/create/group', 'DirController@createGroup')
        ->name('dir.create_group');

    Route::get('dirs/group/{group_dir_available}/create/form', 'DirController@createForm')
        ->where('group_dir_available', '[0-9]+')
        ->name('dir.create_form');
    Route::post('dirs/group/{group_dir_available}/form', 'DirController@storeForm')
        ->where('group_dir_available', '[0-9]+')
        ->name('dir.store_form');

    Route::get('dirs/group/{group_dir_available}/create/summary', 'DirController@createSummary')
        ->where('group_dir_available', '[0-9]+')
        ->name('dir.create_summary');
    Route::post('dirs/group/{group_dir_available}/summary', 'DirController@storeSummary')
        ->where('group_dir_available', '[0-9]+')
        ->name('dir.store_summary');

});
