<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Web\FriendController;

Route::get('friends', [FriendController::class, 'index'])
    ->name('friend.index');
