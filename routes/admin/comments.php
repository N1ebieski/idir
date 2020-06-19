<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'comments/dir/index', 'Comment\Dir\CommentController@index')
    ->name('comment.dir.index')
    ->middleware('permission:admin.comments.view');

Route::get('comments/dir/{dir}/create', 'Comment\Dir\CommentController@create')
    ->name('comment.dir.create')
    ->middleware('permission:admin.comments.create')
    ->where('dir', '[0-9]+');
Route::post('comments/dir/{dir}', 'Comment\Dir\CommentController@store')
    ->name('comment.dir.store')
    ->middleware('permission:admin.comments.create')
    ->where('dir', '[0-9]+');
