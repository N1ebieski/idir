<?php

Route::get('categories/dir/search', 'Category\Dir\CategoryController@search')
    ->middleware('auth')
    ->name('category.dir.search');
