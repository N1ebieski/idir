<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Admin\Price\PriceController;

Route::match(['post', 'get'], 'prices/index', [PriceController::class, 'index'])
    ->name('price.index')
    ->middleware('permission:admin.prices.view');

Route::get('prices/{price}/edit', [PriceController::class, 'edit'])
    ->name('price.edit')
    ->where('price', '[0-9]+')
    ->middleware('permission:admin.prices.edit');
Route::put('prices/{price}', [PriceController::class, 'update'])
    ->name('price.update')
    ->where('price', '[0-9]+')
    ->middleware('permission:admin.prices.edit');

Route::get('prices/create', [PriceController::class, 'create'])
    ->name('price.create')
    ->middleware('permission:admin.prices.create');
Route::post('prices', [PriceController::class, 'store'])
    ->name('price.store')
    ->middleware('permission:admin.prices.create');

Route::delete('prices/{price}', [PriceController::class, 'destroy'])
    ->name('price.destroy')
    ->where('price', '[0-9]+')
    ->middleware('permission:admin.prices.delete');
