<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function () {
    Route::get('payments/{payment_dir_pending}/dir', 'Payment\Dir\PaymentController@show')
        ->middleware('can:show,payment_dir_pending')
        ->where('payment_dir_pending', '[0-9A-Za-z-]+')
        ->name('payment.dir.show');

    Route::get('payments/dir/complete', 'Payment\Dir\PaymentController@complete')
        ->name('payment.dir.complete');
});
