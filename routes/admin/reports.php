<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Admin\Report\Dir\ReportController as DirReportController;

Route::get('reports/dir/{dir}', [DirReportController::class, 'show'])
    ->middleware('permission:admin.dirs.view')
    ->name('report.dir.show')
    ->where('dir', '[0-9]+');

Route::delete('reports/dir/{dir}/clear', [DirReportController::class, 'clear'])
    ->middleware('permission:admin.dirs.edit')
    ->name('report.dir.clear')
    ->where('dir', '[0-9]+');
