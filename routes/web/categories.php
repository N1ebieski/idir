<?php

Route::get('categories/dir/search', 'Category\Dir\CategoryController@search')
    ->middleware('auth')
    ->name('category.dir.search');

Route::get('categories/{category_dir_cache}/dir', 'Category\Dir\CategoryController@show')
    ->name('category.dir.show')
    ->where('category_dir_cache', '[0-9A-Za-z,_-]+');