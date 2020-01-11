<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Report\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Http\Controllers\Admin\Report\Dir\Polymorphic;

/**
 * [ReportController description]
 */
class ReportController implements Polymorphic
{
    /**
     * Display all the specified Reports for Dir.
     *
     * @param  Dir  $dir [description]
     * @return JsonResponse          [description]
     */
    public function show(Dir $dir) : JsonResponse
    {
        $reports = $dir->reports()->with('user:id,name')->get();

        return response()->json([
            'success' => '',
            'view' => view('icore::admin.report.show', [
                'reports' => $reports,
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
    public function clear(Dir $dir) : JsonResponse
    {
        $dir->reports()->delete();

        return response()->json([
            'success' => '',
            'view' => view('idir::admin.dir.partials.dir', [
                'dir' => $dir
            ])->render()
        ]);
    }
}
