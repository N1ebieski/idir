<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Web\Category\Dir\CategoryController as DirCategoryController;

Route::match(['post', 'get'], 'categories/{category_dir_cache}/dirs/{region_cache?}', [DirCategoryController::class, 'show'])
    ->name('category.dir.show')
    ->where('category_dir_cache', '[0-9A-Za-z,_-]+')
    ->where('region_cache', '[a-z-]+');
