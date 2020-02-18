<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function () {
    Route::get('contact/dir/{dir}', 'Contact\Dir\ContactController@show')
        ->name('contact.dir.show')
        ->where('dir', '[0-9]+');
    Route::post('contact/dir/{dir}', 'Contact\Dir\ContactController@send')
        ->name('contact.dir.send')
        ->where('dir', '[0-9]+');
});
