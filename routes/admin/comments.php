<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'comments/dir/index', 'Comment\Dir\CommentController@index')
    ->name('comment.dir.index')
    ->middleware('permission:index comments');

Route::get('comments/dir/{dir}/create', 'Comment\Dir\CommentController@create')
    ->name('comment.dir.create')
    ->middleware('permission:create comments')
    ->where('dir', '[0-9]+');
Route::post('comments/dir/{dir}', 'Comment\Dir\CommentController@store')
    ->name('comment.dir.store')
    ->middleware('permission:create comments')
    ->where('dir', '[0-9]+');