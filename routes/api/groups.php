<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Api\Group\GroupController;

Route::match(['post', 'get'], 'groups/index', [GroupController::class, 'index'])
    ->name('group.index');
