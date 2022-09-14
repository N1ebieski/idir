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
use N1ebieski\IDir\Models\Group;
use Illuminate\Database\Eloquent\Builder;

class Create2Load
{
    /**
     * [__construct description]
     * @param Request $request [description]
     */
    public function __construct(Request $request)
    {
        /** @var Group */
        $group = $request->route('group');

        $group->loadCount(['dirs', 'dirsToday'])
            ->load([
                'privileges',
                'fields' => function (Builder $query) {
                    $query->orderBy('position', 'asc');
                }
            ]);
    }
}
