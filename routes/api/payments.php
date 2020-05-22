<?php

use Illuminate\Support\Facades\Route;

Route::post('payments/dir/verify', 'Payment\Dir\PaymentController@verify')
    ->name('payment.dir.verify');
