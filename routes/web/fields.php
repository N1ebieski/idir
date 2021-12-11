<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Web\FieldController;

Route::post('fields/gus', [FieldController::class, 'gus'])
    ->name('field.gus')
    ->where('field', '[0-9]+');
