<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use N1ebieski\IDir\Models\DirStatus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Response;
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
        $dirStatus->makeService()->delay($request->input('delay'));

        $dirStatus->dir->makeService()->activate();

        Event::dispatch(App::make(DirStatusDelayEvent::class, ['dirStatus' => $dirStatus]));

        return Response::json([
            'view' => View::make('idir::admin.dir.partials.dir', [
                'dir' => $dirStatus->dir->loadAllRels()
            ])
            ->render()
        ]);
    }
}
