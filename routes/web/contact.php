<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Web\Contact\Dir\ContactController as DirContactController;

Route::group(['middleware' => 'auth'], function () {
    Route::get('contact/dir/{dir}', [DirContactController::class, 'show'])
        ->name('contact.dir.show')
        ->where('dir', '[0-9]+');
    Route::post('contact/dir/{dir}', [DirContactController::class, 'send'])
        ->name('contact.dir.send')
        ->where('dir', '[0-9]+');
});
