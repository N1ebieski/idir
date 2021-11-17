<?php

namespace N1ebieski\IDir\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use N1ebieski\IDir\Models\DirStatus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use N1ebieski\IDir\Http\Requests\Admin\DirStatus\DelayRequest;
use N1ebieski\IDir\Events\Admin\DirStatus\DelayEvent as DirStatusDelayEvent;

class DirStatusController
{
    /**
     * Undocumented function
     *
     * @param DirStatus $dirStatus
     * @param DelayRequest $request
     * @return JsonResponse
     */
    public function delay(DirStatus $dirStatus, DelayRequest $request): JsonResponse
    {
        $dirStatus->makeService()->delay($request->only('delay'));

        $dirStatus->dir->makeRepo()->activate();

        Event::dispatch(App::make(DirStatusDelayEvent::class, ['dirStatus' => $dirStatus]));

        return Response::json([
            'success' => '',
            'view' => View::make('idir::admin.dir.partials.dir', [
                'dir' => $dirStatus->dir->loadAllRels()
            ])
            ->render()
        ]);
    }
}
