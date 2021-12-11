<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Web\Payment\Dir\PaymentController as DirPaymentController;

Route::get('payments/{payment_dir_pending}/dir/{driver?}', [DirPaymentController::class, 'show'])
    ->where('payment_dir_pending', '[0-9A-Za-z-]+')
    ->where('driver', '[0-9A-Za-z-]+')
    ->name('payment.dir.show');

Route::get('payments/dir/complete/{driver?}', [DirPaymentController::class, 'complete'])
    ->name('payment.dir.complete')
    ->where('driver', '[0-9A-Za-z-]+');
