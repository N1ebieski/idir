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

namespace N1ebieski\IDir\Loads\Admin\Dir;

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\IDir\Models\Category\Dir\Category;

class EditLoad
{
    /**
     * [__construct description]
     * @param Request $request [description]
     */
    public function __construct(Request $request)
    {
        /** @var Dir */
        $dir = $request->route('dir');

        $dir->load([
            'group',
            'group.privileges',
            'group.fields' => function (Builder $query) {
                $query->orderBy('position', 'asc');
            },
            'regions',
            'categories' => function (Builder|Category $query) {
                $query->withAncestorsExceptSelf();
            },
            'tags'
        ]);
    }
}
