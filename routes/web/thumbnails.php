<?php

use Illuminate\Support\Facades\Route;

Route::get('thumbnails', 'ThumbnailController@show')
    ->middleware('throttle:1000,1')
    ->name('thumbnail.show');