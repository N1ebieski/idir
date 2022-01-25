<?php

namespace N1ebieski\IDir\Http\Controllers\Web;

use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\Auth;
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
        return Response::view('idir::web.profile.dirs', [
            'filter' => $filter->all(),
            'groups' => $group->makeRepo()->getPublic(),
            'dirs' => Auth::user()->makeRepo()->paginateDirsByFilter($filter->all()),
            'paginate' => Config::get('database.paginate')
        ]);
    }
}
