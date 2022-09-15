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
use Illuminate\Support\Facades\Event;
use N1ebieski\IDir\Models\DirBacklink;
use Illuminate\Support\Facades\Response;
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
        $dirBacklink->makeService()->delay($request->input('delay'));

        $dirBacklink->dir->makeService()->activate();

        Event::dispatch(App::make(DirBacklinkDelayEvent::class, ['dirBacklink' => $dirBacklink]));

        return Response::json([
            'view' => View::make('idir::admin.dir.partials.dir', [
                'dir' => $dirBacklink->dir->loadAllRels()
            ])
            ->render()
        ]);
    }
}
