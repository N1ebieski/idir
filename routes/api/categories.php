<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Api\Category\Dir\CategoryController as DirCategoryController;

Route::match(['post', 'get'], 'categories/dir/index', [DirCategoryController::class, 'index'])
    ->name('category.dir.index');
