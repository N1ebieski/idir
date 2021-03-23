<?php

use Illuminate\Support\Facades\Route;

Route::get('payments/{payment_dir_pending}/dir/{driver?}', 'Payment\Dir\PaymentController@show')
    ->where('payment_dir_pending', '[0-9A-Za-z-]+')
    ->where('driver', '[0-9A-Za-z-]+')
    ->name('payment.dir.show');

Route::get('payments/dir/{dir}/logs', 'Payment\Dir\PaymentController@showLogs')
    ->middleware('permission:admin.dirs.view')
    ->name('payment.dir.show_logs')
    ->where('dir', '[0-9]+');
