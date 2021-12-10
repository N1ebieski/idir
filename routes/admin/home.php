<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@index')
    ->name('home.index')
    ->middleware('permission:admin.home.view');
