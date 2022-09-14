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

namespace N1ebieski\IDir\Http\Controllers\Web\Stat\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Models\Stat\Dir\Stat;
use N1ebieski\IDir\Http\Requests\Web\Stat\Dir\ClickRequest;
use N1ebieski\IDir\Http\Controllers\Web\Stat\Dir\Polymorphic;

class StatController implements Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Stat $stat
     * @param Dir $dir
     * @param ClickRequest $request
     * @return JsonResponse
     */
    public function click(Stat $stat, Dir $dir, ClickRequest $request): JsonResponse
    {
        $stat->setRelations(['morph' => $dir])->makeService()->increment();

        return Response::json([]);
    }
}
