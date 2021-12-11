<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Web\ThumbnailController;

Route::get('thumbnails', [ThumbnailController::class, 'show'])
    ->name('thumbnail.show');
