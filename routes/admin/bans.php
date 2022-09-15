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
use N1ebieski\IDir\Http\Controllers\Admin\BanModel\Dir\BanModelController as DirBanModelController;

Route::get('bans/dir/{dir}/create', [DirBanModelController::class, 'create'])
    ->name('banmodel.dir.create')
    ->where('dir', '[0-9]+')
    ->middleware('permission:admin.bans.create');
Route::post('bans/dir/{dir}', [DirBanModelController::class, 'store'])
    ->name('banmodel.dir.store')
    ->where('dir', '[0-9]+')
    ->middleware('permission:admin.bans.create');
