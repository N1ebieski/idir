<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Api\Payment\Dir\PaymentController as DirPaymentController;

Route::post('payments/dir/verify/{driver?}', [DirPaymentController::class, 'verify'])
    ->name('payment.dir.verify')
    ->where('driver', '[0-9A-Za-z-]+');
