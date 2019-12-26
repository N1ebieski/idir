<?php

namespace N1ebieski\IDir\Http\Controllers\Admin;

use N1ebieski\IDir\Filters\Admin\Dir\IndexFilter;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\IDir\Http\Requests\Admin\Dir\IndexRequest;
use N1ebieski\IDir\Http\Requests\Admin\Dir\DestroyRequest;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use N1ebieski\IDir\Events\Admin\Dir\Destroy as DirDestroy;

/**
 * [DirController description]
 */
class DirController
{
    /**
     * [index description]
     * @param  Dir          $dir      [description]
     * @param  Group        $group    [description]
     * @param  Category     $category [description]
     * @param  IndexRequest $request  [description]
     * @param  IndexFilter  $filter   [description]
     * @return View                   [description]
     */
    public function index(
        Dir $dir,
        Group $group,
        Category $category,
        IndexRequest $request,
        IndexFilter $filter
    ) : View
    {
        return view('idir::admin.dir.index', [
            'dirs' => $dir->makeRepo()->paginateByFilter($filter->all() + [
                'except' => $request->input('except')
            ]),
            'groups' => $group->makeRepo()->all(),
            'categories' => $category->makeService()->getAsFlatTree(),
            'filter' => $filter->all()
        ]);
    }

    /**
     * [destroy description]
     * @param  Dir            $dir     [description]
     * @param  DestroyRequest $request [description]
     * @return JsonResponse            [description]
     */
    public function destroy(Dir $dir, DestroyRequest $request) : JsonResponse
    {
        $dir->makeService()->delete();

        event(new DirDestroy($dir, $request->input('reason')));

        return response()->json(['success' => '']);
    }
}
