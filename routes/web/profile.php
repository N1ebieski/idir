<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function () {
    Route::match(['get', 'post'], 'profile/edit/dir', 'ProfileController@editDir')
        ->name('profile.edit_dir');
});
