<?php

Route::get('categories/dir/search', 'Category\Dir\CategoryController@search')
    ->name('category.dir.search');

Route::get('categories/{category_dir_cache}/dirs/{region_cache?}', 'Category\Dir\CategoryController@show')
    ->name('category.dir.show')
    ->where('category_dir_cache', '[0-9A-Za-z,_-]+')
    ->where('region_cache', '[a-z-]+');
