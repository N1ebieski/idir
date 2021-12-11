<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Web\Comment\Dir\CommentController as DirCommentController;

Route::group(['middleware' => 'auth'], function () {
    Route::get('comments/dir/{dir}/create', [DirCommentController::class, 'create'])
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:web.comments.create|web.comments.suggest'])
        ->name('comment.dir.create')
        ->where('dir', '[0-9]+');
    Route::post('comments/dir/{dir}', [DirCommentController::class, 'store'])
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:web.comments.create|web.comments.suggest'])
        ->name('comment.dir.store')
        ->where('dir', '[0-9]+');
});
