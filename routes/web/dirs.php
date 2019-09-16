<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function() {
    Route::get('dir/select', 'DirController@select')
        ->name('dir.select');

    Route::get('dir/create', 'DirController@create')
        ->name('dir.create');
});
