<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Report\Dir;

use N1ebieski\IDir\Http\Requests\Web\Report\Dir\CreateRequest;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Report\Dir\Report;
use N1ebieski\IDir\Http\Requests\Web\Report\Dir\StoreRequest;
use Illuminate\Http\JsonResponse;

interface Polymorphic
{
    /**
     * Display all the specified Reports for Dir.
     *
     * @param  Dir      $dir [description]
     * @param CreateRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function create(Dir $dir, CreateRequest $request) : JsonResponse;

    /**
     * Store a newly created Report for Dir in storage.
     *
     * @param  Dir       $dir       [description]
     * @param  Report        $report        [description]
     * @param  StoreRequest  $request       [description]
     * @return JsonResponse                 [description]
     */
    public function store(Dir $dir, Report $report, StoreRequest $request) : JsonResponse;
}
