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

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection as Collect;
use N1ebieski\IDir\Filters\Api\Auth\User\DirsFilter;
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

        /** @var Dir */
        $dir = $user->dirs()->make();

        /** @var Group|null */
        $group = $filter->get('group');

        return $dir->makeResource()
            ->collection($user->makeRepo()->paginateDirsByFilter($filter->all()))
            ->additional(['meta' => [
                'filter' => Collect::make($filter->all())
                    ->replace([
                        'group' => $group instanceof Group ?
                            $group->makeResource()
                            : $group
                    ])
                    ->toArray()
            ]])
            ->response();
    }
}
