<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Admin\Price\PriceController;

Route::match(['post', 'get'], 'prices/index', [PriceController::class, 'index'])
    ->name('price.index')
    ->middleware('permission:admin.prices.view');

Route::get('prices/{price}/edit', [PriceController::class, 'edit'])
    ->middleware('permission:admin.prices.edit')
    ->name('price.edit')
    ->where('price', '[0-9]+');
Route::put('prices/{price}', [PriceController::class, 'update'])
    ->middleware('permission:admin.prices.edit')
    ->name('price.update')
    ->where('price', '[0-9]+');

Route::get('prices/create', [PriceController::class, 'create'])
    ->name('price.create')
    ->middleware('permission:admin.prices.create');
Route::post('prices', [PriceController::class, 'store'])
    ->name('price.store')
    ->middleware('permission:admin.prices.create');

Route::delete('prices/{price}', [PriceController::class, 'destroy'])
    ->middleware('permission:admin.prices.delete')
    ->name('price.destroy')
    ->where('price', '[0-9]+');
