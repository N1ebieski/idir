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

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Models\Region\Region;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\IDir\Filters\Web\Category\Dir\ShowFilter;
use N1ebieski\IDir\Http\Requests\Web\Category\ShowRequest;
use N1ebieski\IDir\Http\Controllers\Web\Category\Dir\Polymorphic;
use N1ebieski\IDir\Events\Web\Category\Dir\ShowEvent as CategoryShowEvent;

class CategoryController implements Polymorphic
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
    public function show(Category $category, Region $region, ShowRequest $request, ShowFilter $filter): HttpResponse
    {
        $dirs = $category->makeCache()->rememberDirsByFilter($filter->all());

        Event::dispatch(App::make(CategoryShowEvent::class, ['dirs' => $dirs]));

        return Response::view('idir::web.category.dir.show', [
            'dirs' => $dirs,
            'region' => $region,
            'filter' => $filter->all(),
            'category' => $category->makeCache()->rememberLoadNestedWithMorphsCountByFilter($filter->all()),
            'catsAsArray' => [
                'ancestors' => $category->ancestors->pluck('id')->toArray(),
                'self' => [$category->id]
            ]
        ]);
    }
}
