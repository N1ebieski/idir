<?php

namespace N1ebieski\IDir\Http\Controllers\Admin;

use N1ebieski\ICore\Models\Link;
use N1ebieski\IDir\Filters\Admin\Dir\IndexFilter;
use N1ebieski\IDir\Http\Requests\Admin\Dir\Store2Request;
use N1ebieski\IDir\Http\Requests\Admin\Dir\Store3Request;
use N1ebieski\IDir\Http\Requests\Admin\Dir\UpdateRequest;
use N1ebieski\IDir\Http\Requests\Admin\Dir\Create2Request;
use N1ebieski\IDir\Http\Requests\Admin\Dir\Create3Request;
use N1ebieski\IDir\Http\Requests\Admin\Dir\EditFull2Request;
use N1ebieski\IDir\Http\Requests\Admin\Dir\EditFull3Request;
use N1ebieski\IDir\Http\Requests\Admin\Dir\Store3CodeRequest;
use N1ebieski\IDir\Http\Requests\Admin\Dir\UpdateFull2Request;
use N1ebieski\IDir\Http\Requests\Admin\Dir\UpdateFull3Request;
use N1ebieski\IDir\Http\Requests\Admin\Dir\UpdateFull3CodeRequest;
use N1ebieski\IDir\Http\Responses\Admin\Dir\Store3Response;
use N1ebieski\IDir\Http\Responses\Admin\Dir\UpdateFull3Response;
use N1ebieski\IDir\Loads\Admin\Dir\EditLoad;
use N1ebieski\IDir\Loads\Admin\Dir\Store2Load;
use N1ebieski\IDir\Loads\Admin\Dir\Store3Load;
use N1ebieski\IDir\Loads\Admin\Dir\Create2Load;
use N1ebieski\IDir\Loads\Admin\Dir\Create3Load;
use N1ebieski\IDir\Loads\Admin\Dir\EditFull1Load;
use N1ebieski\IDir\Loads\Admin\Dir\EditFull2Load;
use N1ebieski\IDir\Loads\Admin\Dir\EditFull3Load;
use N1ebieski\IDir\Loads\Admin\Dir\UpdateFull2Load;
use N1ebieski\IDir\Loads\Admin\Dir\UpdateFull3Load;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Http\Requests\Admin\Dir\IndexRequest;
use N1ebieski\IDir\Http\Requests\Admin\Dir\DestroyRequest;
use N1ebieski\IDir\Http\Requests\Admin\Dir\DestroyGlobalRequest;
use N1ebieski\IDir\Http\Requests\Admin\Dir\UpdateStatusRequest;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use N1ebieski\IDir\Events\Admin\Dir\DestroyEvent as DirDestroyEvent;
use N1ebieski\IDir\Events\Admin\Payment\Dir\StoreEvent as PaymentStoreEvent;
use N1ebieski\IDir\Events\Admin\Dir\StoreEvent as DirStoreEvent;
use N1ebieski\IDir\Events\Admin\Dir\UpdateFullEvent as DirUpdateFullEvent;
use N1ebieski\IDir\Events\Admin\Dir\UpdateStatusEvent as DirUpdateStatusEvent;
use N1ebieski\IDir\Loads\Admin\Dir\DestroyLoad;

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
    ) : View {
        return view('idir::admin.dir.index', [
            'dirs' => $dir->makeRepo()->paginateForAdminByFilter($filter->all() + [
                'except' => $request->input('except')
            ]),
            'groups' => $group->all(),
            'categories' => $category->makeService()->getAsFlatTree(),
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
        return view('idir::admin.dir.create.1', [
            'groups' => $group->makeRepo()->getWithRels()
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
        return view('idir::admin.dir.create.2', compact('group'));
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

        return redirect()->route('admin.dir.create_3', [$group->id]);
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
    ) : View {
        $dir->makeService()->createOrUpdateSession($request->validated());

        $categories = $category->makeRepo()->getByIds(
            $request->session()->get('dir.categories')
        );

        $backlinks = $group->backlink > 0 ?
            $link->makeRepo()->getAvailableBacklinksByCats(array_merge(
                $categories->pluck('ancestors')->flatten()->pluck('id')->toArray(),
                $categories->pluck('id')->toArray()
            )) : null;

        return view('idir::admin.dir.create.3', compact('group', 'categories', 'backlinks'));
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
    ) : RedirectResponse {
        $dir->setGroup($group)->makeService()->create($request->validated());

        if (($payment = $dir->getPayment()) instanceof Payment) {
            event(new PaymentStoreEvent($payment));
        }

        event(new DirStoreEvent($dir));

        return $response->setDir($dir)->makeResponse();
    }

    /**
     * [edit1 description]
     * @param  Dir       $dir   [description]
     * @param  EditFull1Load $load  [description]
     * @param  Group     $group [description]
     * @return View             [description]
     */
    public function editFull1(Dir $dir, EditFull1Load $load, Group $group) : View
    {
        $dir->makeService()->createOrUpdateSession($dir->attributes_as_array);

        return view('idir::admin.dir.edit_full.1', [
            'dir' => $dir,
            'groups' => $group->makeRepo()->getWithRels()
        ]);
    }

    /**
     * [edit2 description]
     * @param  Dir              $dir     [description]
     * @param  Group            $group   [description]
     * @param  EditFull2Load    $load    [description]
     * @param  EditFull2Request $request [description]
     * @return View                      [description]
     */
    public function editFull2(Dir $dir, Group $group, EditFull2Load $load, EditFull2Request $request) : View
    {
        return view('idir::admin.dir.edit_full.2', compact('dir', 'group'));
    }

    /**
     * [updateFull2 description]
     * @param  Dir              $dir     [description]
     * @param  Group            $group   [description]
     * @param  UpdateFull2Load      $load    [description]
     * @param  UpdateFull2Request   $request [description]
     * @return RedirectResponse          [description]
     */
    public function updateFull2(
        Dir $dir,
        Group $group,
        UpdateFull2Load $load,
        UpdateFull2Request $request
    ) : RedirectResponse {
        $dir->makeService()->createOrUpdateSession($request->validated());

        return redirect()->route('admin.dir.edit_full_3', [$dir->id, $group->id]);
    }

    /**
     * [editFull3 description]
     * @param  Group              $group    [description]
     * @param  Dir                $dir      [description]
     * @param  Category           $category [description]
     * @param  Link               $link     [description]
     * @param  EditFull3Load      $load     [description]
     * @param  EditFull3Request   $request  [description]
     * @return View                         [description]
     */
    public function editFull3(
        Dir $dir,
        Group $group,
        Category $category,
        Link $link,
        EditFull3Load $load,
        EditFull3Request $request
    ) : View {
        $dir->makeService()->createOrUpdateSession($request->validated());

        $categories = $category->makeRepo()->getByIds(
            $request->session()->get("dirId.{$dir->id}.categories")
        );

        $backlinks = $group->backlink > 0 ?
            $link->makeRepo()->getAvailableBacklinksByCats(array_merge(
                $categories->pluck('ancestors')->flatten()->pluck('id')->toArray(),
                $categories->pluck('id')->toArray()
            )) : null;

        return view(
            'idir::admin.dir.edit_full.3',
            compact('dir', 'group', 'categories', 'backlinks')
        );
    }

    /**
     * [updateFull3 description]
     * @param  Dir                     $dir            [description]
     * @param  Group                   $group          [description]
     * @param  UpdateFull3Load         $load           [description]
     * @param  UpdateFull3Request      $request        [description]
     * @param  UpdateFull3CodeRequest  $requestPayment [description]
     * @param  UpdateFull3Response     $response       [description]
     * @return RedirectResponse                        [description]
     */
    public function updateFull3(
        Dir $dir,
        Group $group,
        UpdateFull3Load $load,
        UpdateFull3Request $request,
        UpdateFull3CodeRequest $requestPayment,
        UpdateFull3Response $response
    ) : RedirectResponse {
        $dir->setGroup($group)->makeService()->updateFull($request->validated());

        if (($payment = $dir->getPayment()) instanceof Payment) {
            event(new PaymentStoreEvent($payment));
        }

        event(new DirUpdateFullEvent($dir));

        return $response->setDir($dir)->makeResponse();
    }

    /**
     * [edit description]
     * @param  Dir          $dir  [description]
     * @param  EditLoad     $load [description]
     * @return JsonResponse       [description]
     */
    public function edit(Dir $dir, EditLoad $load) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('idir::admin.dir.edit', [
                'dir' => $dir
            ])->render(),
        ]);
    }

    /**
     * [update2 description]
     * @param  Dir              $dir     [description]
     * @param  UpdateRequest   $request [description]
     * @return JsonResponse             [description]
     */
    public function update(Dir $dir, UpdateRequest $request) : JsonResponse
    {
        $dir->setGroup($dir->group)->makeService()->update($request->validated());

        return response()->json([
            'success' => '',
            'view' => view('idir::admin.dir.partials.dir', [
                'dir' => $dir->loadAllRels()
            ])->render()
        ]);
    }

    /**
     * [updateStatus description]
     * @param  Dir                 $dir     [description]
     * @return JsonResponse                 [description]
     */
    public function updateStatus(Dir $dir, UpdateStatusRequest $request) : JsonResponse
    {
        $dir->makeService()->updateStatus($request->only('status'));

        $dir->loadAllRels();

        event(new DirUpdateStatusEvent($dir));

        return response()->json([
            'success' => '',
            'status' => $dir->status,
            'view' => view('idir::admin.dir.partials.dir', [
                'dir' => $dir
            ])
            ->render()
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param DestroyLoad $load
     * @param DestroyRequest $request
     * @return JsonResponse
     */
    public function destroy(Dir $dir, DestroyLoad $load, DestroyRequest $request) : JsonResponse
    {
        $dir->makeService()->delete();

        event(new DirDestroyEvent($dir, $request->input('reason')));

        return response()->json(['success' => '']);
    }

    /**
     * Remove the collection of Dirs from storage.
     *
     * @param  Dir                  $dir    [description]
     * @param  DestroyGlobalRequest $request [description]
     * @return RedirectResponse              [description]
     */
    public function destroyGlobal(Dir $dir, DestroyGlobalRequest $request) : RedirectResponse
    {
        $deleted = $dir->makeService()->deleteGlobal($request->input('select'));

        return redirect()->back()->with(
            'success',
            trans('idir::dirs.success.destroy_global', [
                'affected' => $deleted
            ])
        );
    }
}
