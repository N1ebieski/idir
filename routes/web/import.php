<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Web\Import\DirController;
use N1ebieski\IDir\Http\Controllers\Web\Import\CategoryController;

Route::get('import/categories/{category}/dirs', [CategoryController::class, 'show'])
    ->name('import.category.dir.show')
    ->where('category', '[0-9]+');

Route::get('import/dirs/{dir}', [DirController::class, 'show'])
    ->name('import.dir.show')
    ->where('dir', '[0-9]+');
