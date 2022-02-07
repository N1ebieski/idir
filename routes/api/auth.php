<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Api\Auth\TokenController;

Route::post('token', [TokenController::class, 'token'])
    ->name('auth.token.token');
