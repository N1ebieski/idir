<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'auth',
    'namespace' => 'Payment\\' . ucfirst(config('idir.payment.transfer.driver'))
], function() {
    Route::get('payments/{payment_dir_pending}/dir', 'Dir\PaymentController@show')
        ->middleware('can:show,payment_dir_pending')
        ->where('payment_dir_pending', '[0-9]+')
        ->name('payment.dir.show');
});
