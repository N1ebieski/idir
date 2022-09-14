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

namespace N1ebieski\IDir\Http\Controllers\Web\Import;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\IDir\Http\Requests\Web\Import\Category\ShowRequest;

class CategoryController
{
    /**
     * Undocumented function
     *
     * @param Category $category
     * @return RedirectResponse
     */
    public function show(Category $category, ShowRequest $request): RedirectResponse
    {
        return Response::redirectToRoute(
            'web.category.dir.show',
            [
                $category->slug,
                'page' => $request->input('page')
            ],
            HttpResponse::HTTP_MOVED_PERMANENTLY
        );
    }
}
