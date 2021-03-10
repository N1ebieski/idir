<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Report\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Models\Report\Dir\Report;
use N1ebieski\IDir\Http\Requests\Web\Report\Dir\StoreRequest;
use N1ebieski\IDir\Http\Requests\Web\Report\Dir\CreateRequest;
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
        return Response::json([
            'success' => '',
            'view' => View::make('icore::web.report.create', [
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
        $report->setRelations(['morph' => $dir])
            ->makeService()
            ->create($request->only('content'));

        return Response::json([
            'success' => Lang::get('icore::reports.success.store')
        ]);
    }
}
