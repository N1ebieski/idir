<?php

namespace N1ebieski\IDir\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use N1ebieski\IDir\Models\DirBacklink;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use N1ebieski\IDir\Http\Requests\Admin\DirBacklink\DelayRequest;
use N1ebieski\IDir\Events\Admin\DirBacklink\DelayEvent as DirBacklinkDelayEvent;

class DirBacklinkController
{
    /**
     * Undocumented function
     *
     * @param DirBacklink $dirBacklink
     * @param DelayRequest $request
     * @return JsonResponse
     */
    public function delay(DirBacklink $dirBacklink, DelayRequest $request): JsonResponse
    {
        $dirBacklink->makeService()->delay($request->only('delay'));

        $dirBacklink->dir->makeRepo()->activate();

        Event::dispatch(App::make(DirBacklinkDelayEvent::class, ['dirBacklink' => $dirBacklink]));

        return Response::json([
            'success' => '',
            'view' => View::make('idir::admin.dir.partials.dir', [
                'dir' => $dirBacklink->dir->loadAllRels()
            ])
            ->render()
        ]);
    }
}
