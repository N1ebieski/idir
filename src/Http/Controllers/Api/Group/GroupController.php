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

namespace N1ebieski\IDir\Http\Controllers\Api\Group;

use N1ebieski\IDir\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use N1ebieski\IDir\Filters\Api\Group\IndexFilter;
use N1ebieski\IDir\Http\Resources\Group\GroupResource;
use N1ebieski\IDir\Http\Requests\Api\Group\IndexRequest;

/**
 * @group Groups
 *
 * > Routes:
 *
 *     /routes/vendor/idir/api/groups.php
 *
 * > Controller:
 *
 *     N1ebieski\IDir\Http\Controllers\Api\Group\GroupController
 *
 * > Resource:
 *
 *     N1ebieski\IDir\Http\Resources\Group\GroupResource
 */
class GroupController
{
    /**
     * Index of groups
     *
     * @bodyParam filter.visible int Must be one of 1 or 0 (available only for admin.groups.view). Example: 1
     *
     * @apiResourceCollection N1ebieski\IDir\Http\Resources\Group\GroupResource
     * @apiResourceModel N1ebieski\IDir\Models\Group states=applyAltGroup,public,additionalOptionsForEditingContent with=privileges
     *
     * @param Group $group
     * @param IndexRequest $request
     * @param IndexFilter $filter
     * @return JsonResponse
     */
    public function index(Group $group, IndexRequest $request, IndexFilter $filter): JsonResponse
    {
        return App::make(GroupResource::class)
            ->collection($group->makeRepo()->paginateByFilter($filter->all()))
            ->additional(['meta' => ['filter' => $filter->all()]])
            ->response();
    }
}
