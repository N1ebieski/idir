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
use N1ebieski\IDir\Http\Controllers\Admin\Category\Dir\CategoryController as DirCategoryController;

Route::match(['post', 'get'], 'categories/dir/index', [DirCategoryController::class, 'index'])
    ->name('category.dir.index')
    ->middleware('permission:admin.categories.view');

Route::get('categories/dir/create', [DirCategoryController::class, 'create'])
    ->name('category.dir.create')
    ->middleware('permission:admin.categories.create');
Route::post('categories/dir', [DirCategoryController::class, 'store'])
    ->name('category.dir.store')
    ->middleware('permission:admin.categories.create');
Route::post('categories/dir/json', [DirCategoryController::class, 'storeGlobal'])
    ->name('category.dir.store_global')
    ->middleware('permission:admin.categories.create');
