<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function() {
    Route::get('profile/edit/dir', 'ProfileController@editDir')
        ->name('profile.edit_dir');
});
