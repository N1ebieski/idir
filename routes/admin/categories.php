<?php

Route::get('categories/dir/search', 'Category\Dir\CategoryController@search')
    ->middleware(['permission:admin.dirs.create|admin.dirs.edit'])
    ->name('category.dir.search');
Route::get('categories/backlink/search', 'Category\Dir\CategoryController@search')
    ->middleware('permission:admin.categories.view')
    ->name('category.backlink.search');

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
