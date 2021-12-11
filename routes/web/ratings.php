<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Web\Rating\Dir\RatingController as DirRatingController;

Route::group(['middleware' => 'auth'], function () {
    Route::get('ratings/dir/{dir}/rate', [DirRatingController::class, 'rate'])
        ->name('rating.dir.rate')
        ->where('dir', '[0-9]+');
});
