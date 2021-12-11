<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Web\HomeController;

Route::get('/', [HomeController::class, 'index'])
    ->name('home.index');
