<?php

use Illuminate\Support\Facades\Route;

Route::get('payments/{payment_dir_pending}/dir', 'Payment\Dir\PaymentController@show')
    ->where('payment_dir_pending', '[0-9A-Za-z-]+')
    ->name('payment.dir.show');

Route::get('payments/dir/complete', 'Payment\Dir\PaymentController@complete')
    ->name('payment.dir.complete');
