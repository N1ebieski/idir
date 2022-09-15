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
use N1ebieski\IDir\Http\Controllers\Web\Stat\Dir\StatController as DirStatController;

Route::get('stats/{stat_dir_cache}/dir/{dir_cache}', [DirStatController::class, 'click'])
    ->where('stat_dir_cache', 'click')
    ->where('dir_cache', '[0-9A-Za-z,_-]+')
    ->name('stat.dir.click');
