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
use N1ebieski\IDir\Http\Controllers\Web\Import\DirController;
use N1ebieski\IDir\Http\Controllers\Web\Import\CategoryController;

Route::get('import/categories/{category}/dirs', [CategoryController::class, 'show'])
    ->name('import.category.dir.show')
    ->where('category', '[0-9]+');

Route::get('import/dirs/{dir}', [DirController::class, 'show'])
    ->name('import.dir.show')
    ->where('dir', '[0-9]+');
