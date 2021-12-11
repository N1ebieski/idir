<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\IDir\Http\Controllers\Web\Report\Dir\ReportController as DirReportController;

Route::group(['middleware' => 'auth'], function () {
    Route::get('reports/dir/{dir}/create', [DirReportController::class, 'create'])
        ->name('report.dir.create')
        ->where('dir', '[0-9]+');
    Route::post('reports/dir/{dir}', [DirReportController::class, 'store'])
        ->name('report.dir.store')
        ->where('dir', '[0-9]+');
});
