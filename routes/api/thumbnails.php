<?php

use Illuminate\Support\Facades\Route;

Route::patch('thumbnails/reload', 'ThumbnailController@reload')
    ->middleware(\N1ebieski\IDir\Http\Middleware\Api\Thumbnail\VerifyKey::class)
    ->name('thumbnail.reload');