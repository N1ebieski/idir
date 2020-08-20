<?php

use Illuminate\Support\Facades\Route;

Route::get('import/categories/{category}/dirs', 'Import\CategoryController@show')
    ->name('import.category.dir.show')
    ->where('category', '[0-9]+');

Route::get('import/dirs/{dir}', 'Import\DirController@show')
    ->name('import.dir.show')
    ->where('dir', '[0-9]+');
