<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Web\Tag\Dir\TagController as DirTagController;

Route::match(['post', 'get'], 'tags/{tag_cache}/dirs', [DirTagController::class, 'show'])
    ->name('tag.dir.show')
    ->where('tag_cache', '[0-9A-Za-z,_-]+');
