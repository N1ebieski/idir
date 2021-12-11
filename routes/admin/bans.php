<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Admin\BanModel\Dir\BanModelController as DirBanModelController;

Route::get('bans/dir/{dir}/create', [DirBanModelController::class, 'create'])
    ->middleware('permission:admin.bans.create')
    ->name('banmodel.dir.create')
    ->where('dir', '[0-9]+');
Route::post('bans/dir/{dir}', [DirBanModelController::class, 'store'])
    ->middleware('permission:admin.bans.create')
    ->name('banmodel.dir.store')
    ->where('dir', '[0-9]+');
