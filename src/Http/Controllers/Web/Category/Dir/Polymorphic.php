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

namespace N1ebieski\IDir\Http\Controllers\Web\Category\Dir;

use N1ebieski\IDir\Models\Region\Region;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\IDir\Filters\Web\Category\Dir\ShowFilter;
use N1ebieski\IDir\Http\Requests\Web\Category\ShowRequest;

interface Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Category $category
     * @param Region $region
     * @param ShowRequest $request
     * @param ShowFilter $filter
     * @return HttpResponse
     */
    public function show(Category $category, Region $region, ShowRequest $request, ShowFilter $filter): HttpResponse;
}
