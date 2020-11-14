<?php

use Illuminate\Support\Facades\Route;

Route::post('fields/gus', 'FieldController@gus')
    ->name('field.gus')
    ->where('field', '[0-9]+');
