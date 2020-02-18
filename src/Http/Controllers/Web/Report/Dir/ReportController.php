<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Report\Dir;

use N1ebieski\IDir\Http\Requests\Web\Report\Dir\CreateRequest;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Report\Dir\Report;
use N1ebieski\IDir\Http\Requests\Web\Report\Dir\StoreRequest;
use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Http\Controllers\Web\Report\Dir\Polymorphic;

class ReportController implements Polymorphic
{
    /**
     * Display all the specified Reports for Dir.
     *
     * @param  Dir  $dir [description]
     * @param CreateRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function create(Dir $dir, CreateRequest $request) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('icore::web.report.create', [
                'model' => $dir
            ])->render()
        ]);
    }

    /**
     * Store a newly created Report for Dir in storage.
     *
     * @param  Dir       $dir       [description]
     * @param  Report        $report        [description]
     * @param  StoreRequest  $request       [description]
     * @return JsonResponse                 [description]
     */
    public function store(Dir $dir, Report $report, StoreRequest $request) : JsonResponse
    {
        $report->setMorph($dir)->makeService()->create($request->only('content'));

        return response()->json([
            'success' => trans('icore::reports.success.store')
        ]);
    }
}
