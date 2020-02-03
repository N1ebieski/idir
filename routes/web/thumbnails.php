<?php

use Illuminate\Support\Facades\Route;

Route::get('thumbnails', 'ThumbnailController@show')
    ->name('thumbnail.show');