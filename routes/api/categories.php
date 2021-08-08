<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'categories/dir/index', 'Category\Dir\CategoryController@index')
    ->name('category.dir.index');
