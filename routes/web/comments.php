<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function() {
    Route::get('comments/dir/{dir}/create', 'Comment\Dir\CommentController@create')
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:create comments|suggest comments'])
        ->name('comment.dir.create')
        ->where('dir', '[0-9]+');
    Route::post('comments/dir/{dir}', 'Comment\Dir\CommentController@store')
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:create comments|suggest comments'])
        ->name('comment.dir.store')
        ->where('dir', '[0-9]+');
});