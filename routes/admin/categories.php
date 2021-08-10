<?php

Route::match(['get', 'post'], 'categories/dir/index', 'Category\Dir\CategoryController@index')
    ->name('category.dir.index')
    ->middleware('permission:admin.categories.view');

Route::get('categories/dir/create', 'Category\Dir\CategoryController@create')
    ->name('category.dir.create')
    ->middleware('permission:admin.categories.create');
Route::post('categories/dir', 'Category\Dir\CategoryController@store')
    ->name('category.dir.store')
    ->middleware('permission:admin.categories.create');
Route::post('categories/dir/json', 'Category\Dir\CategoryController@storeGlobal')
    ->name('category.dir.store_global')
    ->middleware('permission:admin.categories.create');
