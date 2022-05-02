<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Report\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Http\Controllers\Admin\Report\Dir\Polymorphic;

class ReportController implements Polymorphic
{
    /**
     * Display all the specified Reports for Dir.
     *
     * @param  Dir  $dir [description]
     * @return JsonResponse          [description]
     */
    public function show(Dir $dir): JsonResponse
    {
        return Response::json([
            'view' => View::make('icore::admin.report.show', [
                'reports' => $dir->makeRepo()->getReportsWithUser(),
                'model' => $dir
            ])->render()
        ]);
    }

    /**
     * Clear all Reports for specified Dir.
     *
     * @param  Dir $dir [description]
     * @return JsonResponse         [description]
     */
    public function clear(Dir $dir): JsonResponse
    {
        $dir->reports()->delete();

        return Response::json([
            'view' => View::make('idir::admin.dir.partials.dir', [
                'dir' => $dir->loadAllRels()
            ])->render()
        ]);
    }
}
