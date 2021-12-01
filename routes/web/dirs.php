<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'dirs/index', 'DirController@index')
    ->name('dir.index');

Route::match(['get', 'post'], 'dirs/search', 'DirController@search')
    ->name('dir.search');

Route::match(['get', 'post'], 'dirs/{dir_cache}', 'DirController@show')
    ->name('dir.show')
    ->where('dir_cache', '[0-9A-Za-z,_-]+');

Route::group(['middleware' => ['icore.ban.user', 'icore.ban.ip']], function () {
    Route::get('dirs/create/1', 'DirController@create1')
        ->name('dir.create_1');

    Route::get('dirs/group/{group}/create/2', 'DirController@create2')
        ->where('group', '[0-9]+')
        ->name('dir.create_2');
    Route::post('dirs/group/{group}/2', 'DirController@store2')
        ->where('group', '[0-9]+')
        ->name('dir.store_2');

    Route::get('dirs/group/{group}/create/3', 'DirController@create3')
        ->where('group', '[0-9]+')
        ->name('dir.create_3');
    Route::post('dirs/group/{group}/3', 'DirController@store3')
        ->where('group', '[0-9]+')
        ->name('dir.store_3');

    Route::group(['middleware' => ['auth']], function () {
        Route::delete('dirs/{dir}', 'DirController@destroy')
            ->middleware(['permission:web.dirs.delete', 'can:delete,dir'])
            ->name('dir.destroy')
            ->where('dir', '[0-9]+');

        Route::get('dirs/{dir}/edit/1', 'DirController@edit1')
            ->middleware(['permission:web.dirs.edit', 'can:edit,dir'])
            ->name('dir.edit_1')
            ->where('dir', '[0-9]+');

        Route::get('dirs/{dir}/group/{group}/edit/2', 'DirController@edit2')
            ->middleware(['permission:web.dirs.edit', 'can:edit,dir'])
            ->name('dir.edit_2')
            ->where('group', '[0-9]+')
            ->where('dir', '[0-9]+');
        Route::put('dirs/{dir}/group/{group}/2', 'DirController@update2')
            ->middleware(['permission:web.dirs.edit', 'can:edit,dir'])
            ->name('dir.update_2')
            ->where('group', '[0-9]+')
            ->where('dir', '[0-9]+');

        Route::get('dirs/{dir}/group/{group}/edit/3', 'DirController@edit3')
            ->middleware(['permission:web.dirs.edit', 'can:edit,dir'])
            ->name('dir.edit_3')
            ->where('group', '[0-9]+')
            ->where('dir', '[0-9]+');
        Route::put('dirs/{dir}/group/{group}/3', 'DirController@update3')
            ->middleware(['permission:web.dirs.edit', 'can:edit,dir'])
            ->name('dir.update_3')
            ->where('group', '[0-9]+')
            ->where('dir', '[0-9]+');

        Route::get('dirs/{dir}/edit/renew', 'DirController@editRenew')
            ->middleware(['permission:web.dirs.edit', 'can:edit,dir'])
            ->name('dir.edit_renew')
            ->where('dir', '[0-9]+');
        Route::patch('dirs/{dir}/renew', 'DirController@updateRenew')
            ->middleware(['permission:web.dirs.edit', 'can:edit,dir'])
            ->name('dir.update_renew')
            ->where('dir', '[0-9]+');
    });
});
