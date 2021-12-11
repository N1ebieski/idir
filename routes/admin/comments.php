<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Admin\Comment\Dir\CommentController as DirCommentController;

Route::match(['get', 'post'], 'comments/dir/index', [DirCommentController::class, 'index'])
    ->name('comment.dir.index')
    ->middleware('permission:admin.comments.view');

Route::get('comments/dir/{dir}/create', [DirCommentController::class, 'create'])
    ->name('comment.dir.create')
    ->middleware('permission:admin.comments.create')
    ->where('dir', '[0-9]+');
Route::post('comments/dir/{dir}', [DirCommentController::class, 'store'])
    ->name('comment.dir.store')
    ->middleware('permission:admin.comments.create')
    ->where('dir', '[0-9]+');
