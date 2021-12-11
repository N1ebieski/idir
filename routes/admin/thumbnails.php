<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Admin\Thumbnail\Dir\ThumbnailController as DirThumbnailController;

Route::patch('thumbnails/dir/{dir}/reload', [DirThumbnailController::class, 'reload'])
    ->name('thumbnail.dir.reload')
    ->middleware('permission:admin.dirs.view')
    ->where('dir', '[0-9]+');
