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

namespace N1ebieski\IDir\Http\Controllers\Web;

use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Filters\Web\Profile\DirsFilter;
use N1ebieski\IDir\Http\Requests\Web\Profile\DirsRequest;

class ProfileController
{
    /**
     * [dirs description]
     * @param  Group          $group   [description]
     * @param  DirsRequest $request [description]
     * @param  DirsFilter  $filter  [description]
     * @return HttpResponse            [description]
     */
    public function dirs(Group $group, DirsRequest $request, DirsFilter $filter): HttpResponse
    {
        /** @var User */
        $user = $request->user();

        return Response::view('idir::web.profile.dirs', [
            'filter' => $filter->all(),
            'groups' => $group->makeRepo()->getPublic(),
            'dirs' => $user->makeRepo()->paginateDirsByFilter($filter->all()),
            'paginate' => Config::get('database.paginate')
        ]);
    }
}
