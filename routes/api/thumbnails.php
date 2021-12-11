<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Api\ThumbnailController;

Route::patch('thumbnails/reload', [ThumbnailController::class, 'reload'])
    ->middleware(\N1ebieski\IDir\Http\Middleware\Api\Thumbnail\VerifyKey::class)
    ->name('thumbnail.reload');
