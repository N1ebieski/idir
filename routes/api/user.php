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
use N1ebieski\IDir\Http\Controllers\Api\Auth\UserController;

Route::group(['middleware' => 'auth:sanctum', 'permission:api.access'], function () {
    Route::match(['post', 'get'], 'user/dirs', [UserController::class, 'dirs'])
        ->name('user.dirs')
        ->middleware(['permission:api.dirs.edit|api.dirs.delete', 'ability:api.dirs.edit,api.dirs.delete']);
});
