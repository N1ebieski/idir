<?php

namespace N1ebieski\IDir\Http\Controllers\Admin;

use N1ebieski\IDir\Filters\Admin\Dir\IndexFilter;
use N1ebieski\IDir\Models\Dir;
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
     * Display a listing of the Dir.
     *
     * @param  Dir $dir
     * @param  IndexRequest    $request         [description]
     * @param  IndexFilter     $filter          [description]
     * @return View                             [description]
     */
    public function index(Dir $dir, IndexRequest $request, IndexFilter $filter) : View
    {
        $dirs = $dir->paginate();

        return view('idir::admin.dir.index', [
            'dirs' => $dirs,
            'filter' => $filter->all(),
            'paginate' => config('database.paginate')
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
