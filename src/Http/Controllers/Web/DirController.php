<?php

namespace N1ebieski\IDir\Http\Controllers\Web;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use N1ebieski\IDir\Http\Requests\Web\Dir\Edit2Request;
use N1ebieski\IDir\Http\Requests\Web\Dir\Edit3Request;
use N1ebieski\IDir\Http\Requests\Web\Dir\Update2Request;
use N1ebieski\IDir\Http\Requests\Web\Dir\Update3Request;
use N1ebieski\IDir\Http\Requests\Web\Dir\EditRenewRequest;
use N1ebieski\IDir\Http\Requests\Web\Dir\Update3CodeRequest;
use N1ebieski\IDir\Http\Requests\Web\Dir\Create2Request;
use N1ebieski\IDir\Http\Requests\Web\Dir\Store2Request;
use N1ebieski\IDir\Http\Requests\Web\Dir\Store3CodeRequest;
use N1ebieski\IDir\Http\Requests\Web\Dir\Create3Request;
use N1ebieski\IDir\Http\Requests\Web\Dir\Store3Request;
use N1ebieski\IDir\Http\Requests\Web\Dir\UpdateRenewRequest;
use N1ebieski\IDir\Http\Requests\Web\Dir\UpdateRenewCodeRequest;
use N1ebieski\IDir\Loads\Web\Dir\Edit1Load;
use N1ebieski\IDir\Loads\Web\Dir\Edit2Load;
use N1ebieski\IDir\Loads\Web\Dir\Edit3Load;
use N1ebieski\IDir\Loads\Web\Dir\EditRenewLoad;
use N1ebieski\IDir\Loads\Web\Dir\Store2Load;
use N1ebieski\IDir\Loads\Web\Dir\Store3Load;
use N1ebieski\IDir\Loads\Web\Dir\Create2Load;
use N1ebieski\IDir\Loads\Web\Dir\Create3Load;
use N1ebieski\IDir\Loads\Web\Dir\Update2Load;
use N1ebieski\IDir\Loads\Web\Dir\Update3Load;
use N1ebieski\IDir\Loads\Web\Dir\UpdateRenewLoad;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\ICore\Models\Link;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\IDir\Events\Web\Dir\Store as DirStore;
use N1ebieski\IDir\Events\Web\Dir\Update as DirUpdate;
use N1ebieski\IDir\Events\Web\Dir\UpdateRenew as DirUpdateRenew;
use N1ebieski\IDir\Events\Web\Payment\Dir\Store as PaymentStore;
use N1ebieski\IDir\Http\Responses\Web\Dir\Store3Response;
use N1ebieski\IDir\Http\Responses\Web\Dir\Update3Response;
use N1ebieski\IDir\Http\Responses\Web\Dir\UpdateRenewResponse;
use N1ebieski\IDir\Events\Web\Dir\Destroy as DirDestroy;
use N1ebieski\IDir\Filters\Web\Dir\IndexFilter;
use N1ebieski\IDir\Filters\Web\Dir\SearchFilter;
use N1ebieski\IDir\Filters\Web\Dir\ShowFilter;
use N1ebieski\IDir\Http\Requests\Web\Dir\IndexRequest;
use N1ebieski\IDir\Http\Requests\Web\Dir\SearchRequest;
use N1ebieski\IDir\Http\Requests\Web\Dir\ShowRequest;
use N1ebieski\IDir\Loads\Web\Dir\ShowLoad;
use N1ebieski\IDir\Models\Comment\Dir\Comment;

/**
 * [DirController description]
 */
class DirController
{
    /**
     * Display a listing of the Dirs.
     *
     * @param Dir $dir
     * @param IndexRequest $request
     * @param ShowRequest $filter
     * @return View
     */
    public function index(Dir $dir, IndexRequest $request, IndexFilter $filter) : View
    {
        $dirs = $dir->makeCache()->rememberForWebByFilter(
            $filter->all(),
            $request->input('page') ?? 1
        );

        return view('idir::web.dir.index', [
            'dirs' => $dirs,
            'filter' => $filter->all()
        ]);
    }

    /**
     * [search description]
     * @param  Dir           $dir    [description]
     * @param  SearchRequest $request [description]
     * @return View                   [description]
     */
    public function search(Dir $dir, SearchRequest $request, SearchFilter $filter) : View
    {
        return view('idir::web.dir.search', [
            'dirs' => $dir->makeRepo()->paginateBySearchAndFilter(
                $request->input('search'),
                $filter->all()
            ),
            'filter' => $filter->all(),            
            'search' => $request->input('search')
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param Comment $comment
     * @param ShowLoad $load
     * @param ShowRequest $request
     * @param ShowFilter $filter
     * @return View
     */
    public function show(
        Dir $dir, 
        Comment $comment, 
        ShowLoad $load, 
        ShowRequest $request, 
        ShowFilter $filter
    ) : View
    {
        $comments = $comment->setMorph($dir)->makeCache()->rememberRootsByFilter(
            $filter->all() + ['except' => $request->input('except')],
            $request->input('page') ?? 1
        );

        return view('idir::web.dir.show', [
            'dir' => $dir,
            'related' => $dir->makeCache()->rememberRelated(),
            'comments' => $comments,
            'filter' => $filter->all()
        ]);
    }

    /**
     * [create1 description]
     * @param Group     $group     [description]
     * @return View
     */
    public function create1(Group $group) : View
    {
        return view('idir::web.dir.create.1', [
            'groups' => $group->makeRepo()->getPublicWithRels()
        ]);
    }

    /**
     * [create2 description]
     * @param  Group          $group   [description]
     * @param  Create2Load    $load    [description]
     * @param  Create2Request $request [description]
     * @return View                    [description]
     */
    public function create2(Group $group, Create2Load $load, Create2Request $request) : View
    {
        return view('idir::web.dir.create.2', compact('group'));
    }

    /**
     * [store2 description]
     * @param  Group            $group   [description]
     * @param  Dir              $dir     [description]
     * @param  Store2Load       $load    [description]
     * @param  Store2Request    $request [description]
     * @return RedirectResponse          [description]
     */
    public function store2(Group $group, Dir $dir, Store2Load $load, Store2Request $request) : RedirectResponse
    {
        $dir->makeService()->createOrUpdateSession($request->validated());

        return redirect()->route('web.dir.create_3', [$group->id]);
    }

    /**
     * [create3 description]
     * @param  Group          $group    [description]
     * @param  Dir            $dir      [description]
     * @param  Category       $category [description]
     * @param  Link           $link     [description]
     * @param  Create3Load    $load     [description]
     * @param  Create3Request $request  [description]
     * @return View                     [description]
     */
    public function create3(
        Group $group,
        Dir $dir,
        Category $category,
        Link $link,
        Create3Load $load,
        Create3Request $request
    ) : View
    {
        $dir->makeService()->createOrUpdateSession($request->validated());

        $categories = $category->makeRepo()->getByIds(
            $request->session()->get('dir.categories')
        );

        $backlinks = $group->backlink > 0 ? 
            $link->makeRepo()->getAvailableBacklinksByCats(array_merge(
                $categories->pluck('ancestors')->flatten()->pluck('id')->toArray(),
                $categories->pluck('id')->toArray()
            )) : null;

        return view('idir::web.dir.create.3', compact('group', 'categories', 'backlinks'));
    }

    /**
     * [store3 description]
     * @param  Group              $group          [description]
     * @param  Dir                $dir            [description]
     * @param  Store3Load         $load           [description]
     * @param  Store3Request      $request        [description]
     * @param  Store3CodeRequest  $requestPayment [description]
     * @param  Store3Response     $response       [description]
     * @return RedirectResponse                   [description]
     */
    public function store3(
        Group $group,
        Dir $dir,
        Store3Load $load,
        Store3Request $request,
        Store3CodeRequest $requestPayment,
        Store3Response $response
    ) : RedirectResponse
    {
        $dir->setGroup($group)->makeService()->create($request->validated());

        if (($payment = $dir->getPayment()) instanceof Payment) {
            event(new PaymentStore($payment));
        }

        event(new DirStore($dir));

        return $response->setDir($dir)->makeResponse();
    }

    /**
     * [edit1 description]
     * @param  Dir       $dir   [description]
     * @param  Edit1Load $load  [description]
     * @param  Group     $group [description]
     * @return View             [description]
     */
    public function edit1(Dir $dir, Edit1Load $load, Group $group) : View
    {
        $dir->makeService()->createOrUpdateSession($dir->attributes_as_array);

        return view('idir::web.dir.edit.1', [
            'dir' => $dir,
            'groups' => $group->makeRepo()->getPublicWithRels()
        ]);
    }

    /**
     * [edit2 description]
     * @param  Dir          $dir     [description]
     * @param  Group        $group   [description]
     * @param  Edit2Load    $load    [description]
     * @param  Edit2Request $request [description]
     * @return View                  [description]
     */
    public function edit2(Dir $dir, Group $group, Edit2Load $load, Edit2Request $request) : View
    {
        return view('idir::web.dir.edit.2', compact('dir', 'group'));
    }

    /**
     * [update2 description]
     * @param  Dir              $dir     [description]
     * @param  Group            $group   [description]
     * @param  Update2Load      $load    [description]
     * @param  Update2Request   $request [description]
     * @return RedirectResponse          [description]
     */
    public function update2(Dir $dir, Group $group, Update2Load $load, Update2Request $request) : RedirectResponse
    {
        $dir->makeService()->createOrUpdateSession($request->validated());

        return redirect()->route('web.dir.edit_3', [$dir->id, $group->id]);
    }

    /**
     * [edit3 description]
     * @param  Group          $group    [description]
     * @param  Dir            $dir      [description]
     * @param  Category       $category [description]
     * @param  Link           $link     [description]
     * @param  Edit3Load      $load     [description]
     * @param  Edit3Request   $request  [description]
     * @return View                     [description]
     */
    public function edit3(
        Dir $dir,
        Group $group,
        Category $category,
        Link $link,
        Edit3Load $load,
        Edit3Request $request
    ) : View
    {
        $dir->makeService()->createOrUpdateSession($request->validated());

        $categories = $category->makeRepo()->getByIds(
            $request->session()->get("dirId.{$dir->id}.categories")
        );

        $backlinks = $group->backlink > 0 ? 
            $link->makeRepo()->getAvailableBacklinksByCats(array_merge(
                $categories->pluck('ancestors')->flatten()->pluck('id')->toArray(),
                $categories->pluck('id')->toArray()
            )) : null;

        return view('idir::web.dir.edit.3', 
            compact('dir', 'group', 'categories', 'backlinks'));
    }

    /**
     * [store3 description]
     * @param  Dir                 $dir            [description]
     * @param  Group               $group          [description]
     * @param  Update3Load         $load           [description]
     * @param  Update3Request      $request        [description]
     * @param  Update3CodeRequest  $requestPayment [description]
     * @param  Update3Response     $response       [description]
     * @return RedirectResponse                   [description]
     */
    public function update3(
        Dir $dir,
        Group $group,
        Update3Load $load,
        Update3Request $request,
        Update3CodeRequest $requestPayment,
        Update3Response $response
    ) : RedirectResponse
    {
        $dir->setGroup($group)->makeService()->updateFull($request->validated());

        if (($payment = $dir->getPayment()) instanceof Payment) {
            event(new PaymentStore($payment));
        }

        event(new DirUpdate($dir));

        return $response->setDir($dir)->makeResponse();
    }

    /**
     * [editRenew description]
     * @param  Dir              $dir     [description]
     * @param  EditRenewLoad    $load    [description]
     * @param  EditRenewRequest $request [description]
     * @return View                      [description]
     */
    public function editRenew(Dir $dir, EditRenewLoad $load, EditRenewRequest $request) : View
    {
        return view('idir::web.dir.edit_renew', compact('dir'));
    }

    /**
     * [updateRenew description]
     * @param  Dir                    $dir            [description]
     * @param  UpdateRenewLoad        $load           [description]
     * @param  UpdateRenewRequest     $request        [description]
     * @param  UpdateRenewCodeRequest $requestPayment [description]
     * @param  UpdateRenewResponse    $response       [description]
     * @return RedirectResponse                       [description]
     */
    public function updateRenew(
        Dir $dir,
        UpdateRenewLoad $load,
        UpdateRenewRequest $request,
        UpdateRenewCodeRequest $requestPayment,
        UpdateRenewResponse $response
    ) : RedirectResponse
    {
        $payment = $dir->makeService()->createPayment($request->validated());

        event(new PaymentStore($payment));

        event(new DirUpdateRenew($dir));

        return $response->setDir($dir)->makeResponse();
    }

    /**
     * [destroy description]
     * @param  Dir          $dir [description]
     * @return JsonResponse      [description]
     */
    public function destroy(Dir $dir) : JsonResponse
    {
        $dir->makeService()->delete();

        event(new DirDestroy($dir));

        return response()->json(['success' => '']);
    }
}
