<?php

use Illuminate\Support\Facades\Route;

Route::get('friends', 'FriendController@index')->name('friend.index');
