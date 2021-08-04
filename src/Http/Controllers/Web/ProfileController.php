<?php

namespace N1ebieski\IDir\Http\Controllers\Web;

use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Filters\Web\Profile\EditDirFilter;
use N1ebieski\IDir\Http\Requests\Web\Profile\EditDirRequest;

/**
 * [ProfileController description]
 */
class ProfileController
{
    /**
     * [editDir description]
     * @param  Group          $group   [description]
     * @param  EditDirRequest $request [description]
     * @param  EditDirFilter  $filter  [description]
     * @return HttpResponse            [description]
     */
    public function editDir(Group $group, EditDirRequest $request, EditDirFilter $filter) : HttpResponse
    {
        return Response::view('idir::web.profile.edit_dir', [
            'filter' => $filter->all(),
            'groups' => $group->makeRepo()->getPublic(),
            'dirs' => Auth::user()->makeRepo()->paginateDirsByFilter($filter->all()),
            'paginate' => Config::get('database.paginate')
        ]);
    }
}
