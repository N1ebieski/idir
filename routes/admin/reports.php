<?php

use Illuminate\Support\Facades\Route;

Route::get('reports/dir/{dir}', 'Report\Dir\ReportController@show')
    ->middleware('permission:admin.dirs.view')
    ->name('report.dir.show')
    ->where('dir', '[0-9]+');

Route::delete('reports/dir/{dir}/clear', 'Report\Dir\ReportController@clear')
    ->middleware('permission:admin.dirs.edit')
    ->name('report.dir.clear')
    ->where('dir', '[0-9]+');
