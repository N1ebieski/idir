<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Admin\Payment\Dir\PaymentController as DirPaymentController;

Route::get('payments/{payment_dir_pending}/dir/{driver?}', [DirPaymentController::class, 'show'])
    ->where('payment_dir_pending', '[0-9A-Za-z-]+')
    ->where('driver', '[0-9A-Za-z-]+')
    ->name('payment.dir.show');

Route::get('payments/dir/{dir}/logs', [DirPaymentController::class, 'showLogs'])
    ->middleware('permission:admin.dirs.view')
    ->name('payment.dir.show_logs')
    ->where('dir', '[0-9]+');
