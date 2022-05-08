<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\BanModel\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Models\BanValue;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\ValueObjects\BanValue\Type;
use N1ebieski\IDir\Models\BanModel\Dir\BanModel;
use N1ebieski\IDir\Http\Requests\Admin\BanModel\Dir\StoreRequest;
use N1ebieski\IDir\Http\Controllers\Admin\BanModel\Dir\PolymorphicInterface;

class BanModelController implements PolymorphicInterface
{
    /**
     * Show the form for creating a new BanModel.
     *
     * @param  Dir         $dir   [description]
     * @return JsonResponse       [description]
     */
    public function create(Dir $dir): JsonResponse
    {
        return Response::json([
            'view' => View::make('idir::admin.banmodel.dir.create', [
                'dir' => $dir
            ])->render()
        ]);
    }

    /**
     * Store a newly created BanModel, BanValue.ip, BanValue.dir in storage.
     *
     * @param  Dir          $dir      [description]
     * @param  BanModel     $banModel [description]
     * @param  BanValue     $banValue [description]
     * @param  StoreRequest $request  [description]
     * @return JsonResponse           [description]
     */
    public function store(Dir $dir, BanModel $banModel, BanValue $banValue, StoreRequest $request): JsonResponse
    {
        if ($request->has('user')) {
            $banModel->morph()->associate($dir->user)->save();
        }

        if ($request->has('ip')) {
            $banValue->create([
                'type' => Type::IP,
                'value' => $request->input('ip')
            ]);
        }

        if ($request->has('url')) {
            $banValue->create([
                'type' => Type::URL,
                'value' => $request->input('url')
            ]);
        }

        return Response::json([
            'success' => trans('icore::bans.model.success.store'),
        ]);
    }
}
