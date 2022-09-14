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

namespace N1ebieski\IDir\Repositories\Privilege;

use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Privilege;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PrivilegeRepo
{
    /**
     * [__construct description]
     * @param Privilege $privilege [description]
     */
    public function __construct(protected Privilege $privilege)
    {
        //
    }

    /**
     * [getWithRole description]
     * @param  int        $id [description]
     * @return Collection     [description]
     */
    public function getWithGroup(int $id): Collection
    {
        return $this->privilege->newQuery()
            ->with([
                'groups' => function (BelongsToMany|Builder|Group $query) use ($id) {
                    return $query->where('id', $id);
                }
            ])
            ->orderBy('name', 'asc')
            ->get();
    }
}
