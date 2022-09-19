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

namespace N1ebieski\IDir\Http\Controllers\Api\Auth;

use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Collection as Collect;
use N1ebieski\IDir\Http\Resources\Dir\DirResource;
use N1ebieski\IDir\Filters\Api\Auth\User\DirsFilter;
use N1ebieski\IDir\Http\Resources\Group\GroupResource;
use N1ebieski\IDir\Http\Requests\Api\Auth\User\DirsRequest;

/**
 * @group Authenticated user
 *
 * > Routes:
 *
 *     /routes/vendor/icore/api/user.php
 *     /routes/vendor/idir/api/user.php
 *
 * > Controller:
 *
 *     N1ebieski\ICore\Http\Controllers\Api\Auth\UserController
 *     N1ebieski\IDir\Http\Controllers\Api\Auth\UserController
 *
 */
class UserController
{
    /**
     * Index of user's dirs
     *
     * @authenticated
     *
     * @responseField id int
     * @responseField slug string
     * @responseField title string
     * @responseField short_content string A shortened version of the post without HTML formatting.
     * @responseField content string Post without HTML formatting.
     * @responseField content_html string Post with HTML formatting.
     * @responseField less_content_html string Post with HTML formatting with "show more" button.
     * @responseField notes string (available only for admin.dirs.view or owner) Additional infos for moderator.
     * @responseField url string
     * @responseField thumbnail_url string
     * @responseField sum_rating string Average rating for an entry.
     * @responseField status object (available only for api.dirs.view or owner)
     * @responseField privileged_at string (available only for api.dirs.view or owner) Start date of premium time.
     * @responseField priveleged_to string (available only for api.dirs.view or owner) End date of premium time. If null and <code>privileged_at</code> not null then premium time is unlimited.
     * @responseField created_at string
     * @responseField updated_at string
     * @responseField group object (available only for api.dirs.view or owner) Contains relationship Group.
     * @responseField user object (available only for admin.dirs.view or owner) Contains relationship User.
     * @responseField categories object[] Contains relationship Categories.
     * @responseField tags object[] Contains relationship Tags.
     * @responseField fields object[] Contains relationship custom Fields.
     * @responseField links object Contains links to resources on the website and in the administration panel.
     * @responseField meta object Paging, filtering and sorting information.
     *
     * @apiResourceCollection N1ebieski\IDir\Http\Resources\Dir\DirResource
     * @apiResourceModel N1ebieski\IDir\Models\Dir states=titleSentence,contentText,with_user,pending,withCategory,withDefaultGroup with=ratings,categories,group,user
     *
     * @param  DirsRequest $request [description]
     * @param  DirsFilter  $filter  [description]
     * @return JsonResponse            [description]
     */
    public function dirs(DirsRequest $request, DirsFilter $filter): JsonResponse
    {
        /** @var User */
        $user = $request->user();

        return App::make(DirResource::class)
            ->collection($user->makeRepo()->paginateDirsByFilter($filter->all()))
            ->additional(['meta' => [
                'filter' => Collect::make($filter->all())
                    ->replace([
                        'group' => $filter->get('group') instanceof Group ?
                            App::make(GroupResource::class, ['group' => $filter->get('group')])
                            : $filter->get('group')
                    ])
                    ->toArray()
            ]])
            ->response();
    }
}
