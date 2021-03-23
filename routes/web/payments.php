<?php

use Illuminate\Support\Facades\Route;

Route::get('payments/{payment_dir_pending}/dir/{driver?}', 'Payment\Dir\PaymentController@show')
    ->where('payment_dir_pending', '[0-9A-Za-z-]+')
    ->where('driver', '[0-9A-Za-z-]+')
    ->name('payment.dir.show');

Route::get('payments/dir/complete/{driver?}', 'Payment\Dir\PaymentController@complete')
    ->name('payment.dir.complete')
    ->where('driver', '[0-9A-Za-z-]+');
