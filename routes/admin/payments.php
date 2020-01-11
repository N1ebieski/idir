<?php

use Illuminate\Support\Facades\Route;

Route::get('payments/dir/{dir}/logs', 'Payment\Dir\PaymentController@showLogs')
    ->middleware('permission:index dirs')
    ->name('payment.dir.show_logs')
    ->where('dir', '[0-9]+');