<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Web\Comment\Dir\CommentController as DirCommentController;

Route::group(['middleware' => 'auth'], function () {
    Route::get('comments/dir/{dir}/create', [DirCommentController::class, 'create'])
        ->name('comment.dir.create')
        ->where('dir', '[0-9]+')
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:web.comments.create|web.comments.suggest']);
    Route::post('comments/dir/{dir}', [DirCommentController::class, 'store'])
        ->name('comment.dir.store')
        ->where('dir', '[0-9]+')
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:web.comments.create|web.comments.suggest']);
});
