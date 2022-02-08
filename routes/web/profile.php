<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Web\ProfileController;

Route::group(['middleware' => 'auth'], function () {
    Route::match(['post', 'get'], 'profile/dirs', [ProfileController::class, 'dirs'])
        ->name('profile.dirs')
        ->middleware(['api.access', 'permission:web.dirs.edit|web.dirs.delete']);
});
