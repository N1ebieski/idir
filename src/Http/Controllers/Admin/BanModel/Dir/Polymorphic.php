<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\BanModel\Dir;

use N1ebieski\IDir\Http\Requests\Admin\BanModel\Dir\StoreRequest;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\BanModel\Dir\BanModel;
use N1ebieski\IDir\Models\BanValue;
use Illuminate\Http\JsonResponse;

/**
 * [interface description]
 * @var [type]
 */
interface Polymorphic
{
    /**
     * Show the form for creating a new BanModel.
     *
     * @param  Dir         $dir   [description]
     * @return JsonResponse       [description]
     */
    public function create(Dir $dir) : JsonResponse;

    /**
     * Store a newly created BanModel and BanValue.ip in storage.
     *
     * @param  Dir          $dir      [description]
     * @param  BanModel     $banModel [description]
     * @param  BanValue     $banValue [description]
     * @param  StoreRequest $request  [description]
     * @return JsonResponse           [description]
     */
    public function store(Dir $dir, BanModel $banModel, BanValue $banValue, StoreRequest $request) : JsonResponse;
}
