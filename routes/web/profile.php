<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Web\ProfileController;

Route::group(['middleware' => 'auth'], function () {
    Route::match(['get', 'post'], 'profile/dirs', [ProfileController::class, 'dirs'])
        ->name('profile.dirs');
});
