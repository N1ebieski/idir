<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Api\Auth\UserController;

Route::group(['middleware' => 'auth:sanctum', 'permission:api.access'], function () {
    Route::match(['post', 'get'], 'user/dirs', [UserController::class, 'dirs'])
        ->name('user.dirs')
        ->middleware(['permission:api.dirs.edit|api.dirs.delete', 'ability:api.dirs.edit,api.dirs.delete']);
});
