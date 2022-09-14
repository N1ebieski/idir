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

namespace N1ebieski\IDir\Http\Controllers\Web\Report\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Models\Report\Dir\Report;
use N1ebieski\IDir\Http\Requests\Web\Report\Dir\StoreRequest;
use N1ebieski\IDir\Http\Requests\Web\Report\Dir\CreateRequest;

interface Polymorphic
{
    /**
     * Display all the specified Reports for Dir.
     *
     * @param  Dir      $dir [description]
     * @param CreateRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function create(Dir $dir, CreateRequest $request): JsonResponse;

    /**
     * Store a newly created Report for Dir in storage.
     *
     * @param  Dir       $dir       [description]
     * @param  Report        $report        [description]
     * @param  StoreRequest  $request       [description]
     * @return JsonResponse                 [description]
     */
    public function store(Dir $dir, Report $report, StoreRequest $request): JsonResponse;
}
