<?php

use Illuminate\Support\Facades\Route;

Route::post('payments/dir/verify/{driver?}', 'Payment\Dir\PaymentController@verify')
    ->name('payment.dir.verify')
    ->where('driver', '[0-9A-Za-z-]+');
