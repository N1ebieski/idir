<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'prices/index', 'Price\PriceController@index')
    ->name('price.index')
    ->middleware('permission:admin.prices.view');

Route::get('prices/{price}/edit', 'Price\PriceController@edit')
    ->middleware('permission:admin.prices.edit')
    ->name('price.edit')
    ->where('price', '[0-9]+');
Route::put('prices/{price}', 'Price\PriceController@update')
    ->middleware('permission:admin.prices.edit')
    ->name('price.update')
    ->where('price', '[0-9]+');

Route::get('prices/create', 'Price\PriceController@create')
    ->name('price.create')
    ->middleware('permission:admin.prices.create');
Route::post('prices', 'Price\PriceController@store')
    ->name('price.store')
    ->middleware('permission:admin.prices.create');

Route::delete('prices/{price}', 'Price\PriceController@destroy')
    ->middleware('permission:admin.prices.delete')
    ->name('price.destroy')
    ->where('price', '[0-9]+');