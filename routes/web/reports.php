<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function () {
    Route::get('reports/dir/{dir}/create', 'Report\Dir\ReportController@create')
        ->name('report.dir.create')
        ->where('dir', '[0-9]+');
    Route::post('reports/dir/{dir}', 'Report\Dir\ReportController@store')
        ->name('report.dir.store')
        ->where('dir', '[0-9]+');
});
