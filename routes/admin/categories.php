<?php

Route::get('categories/dir/search', 'Category\Dir\CategoryController@search')
    ->middleware(['permission:create dirs|edit dirs'])
    ->name('category.dir.search');
Route::get('categories/backlink/search', 'Category\Dir\CategoryController@search')
    ->middleware('permission:index categories')
    ->name('category.backlink.search');

Route::get('categories/dir', 'Category\Dir\CategoryController@index')
    ->name('category.dir.index')
    ->middleware('permission:index categories');

Route::get('categories/dir/create', 'Category\Dir\CategoryController@create')
    ->name('category.dir.create')
    ->middleware('permission:create categories');
Route::post('categories/dir', 'Category\Dir\CategoryController@store')
    ->name('category.dir.store')
    ->middleware('permission:create categories');
Route::post('categories/dir/json', 'Category\Dir\CategoryController@storeGlobal')
    ->name('category.dir.store_global')
    ->middleware('permission:create categories');
