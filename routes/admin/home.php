<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Admin\HomeController;

Route::get('/', [HomeController::class, 'index'])
    ->name('home.index')
    ->middleware('permission:admin.home.view');
