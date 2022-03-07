<?php

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
     * @responseField id int
     * @responseField slug string
     * @responseField position int
     * @responseField name string
     * @responseField desc string
     * @responseField border string Class of border.
     * @responseField max_cats int Maximum number of categories to which the entry can be added.
     * @responseField max_models int Maximum number of entries that can be in the group.
     * @responseField max_models_daily int Daily maximum number of entries that can be in the group.
     * @responseField visible int Indicates whether the group is public or not.
     * @responseField apply_status int Entry status after adding.
     * @responseField url int Whether the url is require.
     * @responseField backlink int Whether the backlink is require.
     * @responseField created_at string
     * @responseField created_at_diff string
     * @responseField updated_at string
     * @responseField updated_at_diff string
     * @responseField alt object Contains relationship alternative Group. Informs to which group the entry will be dropped after expiry of the premium time. If null the entry will be deactivate.
     * @responseField privileges object[] Contains relationship Privileges.
     * @responseField prices object[] Contains relationship Prices.
     * @responseField fields object[] Contains relationship custom Fields.
     *
     * @apiResourceCollection N1ebieski\IDir\Http\Resources\Group\GroupResource
     * @apiResourceModel N1ebieski\IDir\Models\Group states=apply_alt_group,public,additional_options_for_editing_content with=privileges
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
