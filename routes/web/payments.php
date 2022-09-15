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
use N1ebieski\IDir\Http\Controllers\Web\Payment\Dir\PaymentController as DirPaymentController;

Route::get('payments/{payment_dir_pending}/dir/{driver?}', [DirPaymentController::class, 'show'])
    ->where('payment_dir_pending', '[0-9A-Za-z-]+')
    ->where('driver', '[0-9A-Za-z-]+')
    ->name('payment.dir.show');

Route::get('payments/dir/complete/{driver?}', [DirPaymentController::class, 'complete'])
    ->name('payment.dir.complete')
    ->where('driver', '[0-9A-Za-z-]+');
