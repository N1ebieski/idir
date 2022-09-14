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

namespace N1ebieski\IDir\Http\Controllers\Admin\Report\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Http\Controllers\Admin\Report\Dir\Polymorphic;

class ReportController implements Polymorphic
{
    /**
     * Display all the specified Reports for Dir.
     *
     * @param  Dir  $dir [description]
     * @return JsonResponse          [description]
     */
    public function show(Dir $dir): JsonResponse
    {
        return Response::json([
            'view' => View::make('icore::admin.report.show', [
                'reports' => $dir->makeRepo()->getReportsWithUser(),
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
    public function clear(Dir $dir): JsonResponse
    {
        $dir->reports()->delete();

        return Response::json([
            'view' => View::make('idir::admin.dir.partials.dir', [
                'dir' => $dir->loadAllRels()
            ])->render()
        ]);
    }
}
