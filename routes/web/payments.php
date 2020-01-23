<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Payment\\' . ucfirst(config('idir.payment.transfer.driver'))], function() {
    Route::group(['middleware' => 'auth',], function() {
        Route::get('payments/{payment_dir_pending}/dir', 'Dir\PaymentController@show')
            ->middleware('can:show,payment_dir_pending')
            ->where('payment_dir_pending', '[0-9A-Za-z-]+')
            ->name('payment.dir.show');

        Route::get('payments/dir/complete', 'Dir\PaymentController@complete')
            ->name('payment.dir.complete');
    });

    Route::post('payments/dir/verify', 'Dir\PaymentController@verify')
        ->name('payment.dir.verify');
});
