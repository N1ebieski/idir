<?php

namespace N1ebieski\IDir\Http\Controllers\Admin;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Loads\Admin\Dir\EditLoad;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Loads\Admin\Dir\Store2Load;
use N1ebieski\IDir\Loads\Admin\Dir\Store3Load;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Loads\Admin\Dir\Create2Load;
use N1ebieski\IDir\Loads\Admin\Dir\Create3Load;
use N1ebieski\IDir\Loads\Admin\Dir\DestroyLoad;
use N1ebieski\IDir\Filters\Admin\Dir\IndexFilter;
use N1ebieski\IDir\Loads\Admin\Dir\EditFull1Load;
use N1ebieski\IDir\Loads\Admin\Dir\EditFull2Load;
use N1ebieski\IDir\Loads\Admin\Dir\EditFull3Load;
use N1ebieski\IDir\Loads\Admin\Dir\UpdateFull2Load;
use N1ebieski\IDir\Loads\Admin\Dir\UpdateFull3Load;
use N1ebieski\IDir\Http\Requests\Admin\Dir\IndexRequest;
use N1ebieski\IDir\Http\Requests\Admin\Dir\Store2Request;
use N1ebieski\IDir\Http\Requests\Admin\Dir\Store3Request;
use N1ebieski\IDir\Http\Requests\Admin\Dir\UpdateRequest;
use N1ebieski\IDir\Http\Requests\Admin\Dir\Create2Request;
use N1ebieski\IDir\Http\Requests\Admin\Dir\Create3Request;
use N1ebieski\IDir\Http\Requests\Admin\Dir\DestroyRequest;
use N1ebieski\IDir\Http\Responses\Admin\Dir\Store3Response;
use N1ebieski\IDir\Http\Requests\Admin\Dir\EditFull2Request;
use N1ebieski\IDir\Http\Requests\Admin\Dir\EditFull3Request;
use N1ebieski\IDir\View\ViewModels\Admin\Dir\Edit1ViewModel;
use N1ebieski\IDir\Http\Requests\Admin\Dir\Store3CodeRequest;
use N1ebieski\IDir\Http\Requests\Admin\Dir\UpdateFull2Request;
use N1ebieski\IDir\Http\Requests\Admin\Dir\UpdateFull3Request;
use N1ebieski\IDir\View\ViewModels\Admin\Dir\Create1ViewModel;
use N1ebieski\IDir\View\ViewModels\Admin\Dir\Create2ViewModel;
use N1ebieski\IDir\View\ViewModels\Admin\Dir\Create3ViewModel;
use N1ebieski\IDir\Http\Requests\Admin\Dir\UpdateStatusRequest;
use N1ebieski\IDir\Events\Admin\Dir\StoreEvent as DirStoreEvent;
use N1ebieski\IDir\Http\Requests\Admin\Dir\DestroyGlobalRequest;
use N1ebieski\IDir\Http\Responses\Admin\Dir\UpdateFull3Response;
use N1ebieski\IDir\View\ViewModels\Admin\Dir\EditFull2ViewModel;
use N1ebieski\IDir\View\ViewModels\Admin\Dir\EditFull3ViewModel;
use N1ebieski\IDir\Http\Requests\Admin\Dir\UpdateFull3CodeRequest;
use N1ebieski\IDir\Events\Admin\Dir\DestroyEvent as DirDestroyEvent;
use N1ebieski\IDir\Events\Admin\Dir\UpdateFullEvent as DirUpdateFullEvent;
use N1ebieski\IDir\Events\Admin\Payment\Dir\StoreEvent as PaymentStoreEvent;
use N1ebieski\IDir\Events\Admin\Dir\UpdateStatusEvent as DirUpdateStatusEvent;

class DirController
{
    /**
     * [index description]
     * @param  Dir          $dir      [description]
     * @param  Group        $group    [description]
     * @param  IndexRequest $request  [description]
     * @param  IndexFilter  $filter   [description]
     * @return HttpResponse           [description]
     */
    public function index(
        Dir $dir,
        Group $group,
        IndexRequest $request,
        IndexFilter $filter
    ): HttpResponse {
        return Response::view('idir::admin.dir.index', [
            'dirs' => $dir->makeRepo()->paginateForAdminByFilter($filter->all()),
            'groups' => $group->orderBy('position', 'asc')->get(),
            'filter' => $filter->all()
        ]);
    }

    /**
     * [create1 description]
     * @return HttpResponse
     */
    public function create1(): HttpResponse
    {
        return Response::view('idir::admin.dir.create.1', App::make(Create1ViewModel::class));
    }

    /**
     * [create2 description]
     * @param  Group          $group   [description]
     * @param  Create2Load    $load    [description]
     * @param  Create2Request $request [description]
     * @return HttpResponse                    [description]
     */
    public function create2(Group $group, Create2Load $load, Create2Request $request): HttpResponse
    {
        return Response::view('idir::admin.dir.create.2', App::make(Create2ViewModel::class, [
            'group' => $group
        ]));
    }

    /**
     * [store2 description]
     * @param  Group            $group   [description]
     * @param  Dir              $dir     [description]
     * @param  Store2Load       $load    [description]
     * @param  Store2Request    $request [description]
     * @return RedirectResponse          [description]
     */
    public function store2(Group $group, Dir $dir, Store2Load $load, Store2Request $request): RedirectResponse
    {
        $dir->setRelations(['group' => $group])
            ->makeService()
            ->createOrUpdateSession($request->validated());

        return Response::redirectToRoute('admin.dir.create_3', [$group->id]);
    }

    /**
     * [create3 description]
     * @param  Group          $group    [description]
     * @param  Dir            $dir      [description]
     * @param  Create3Load    $load     [description]
     * @param  Create3Request $request  [description]
     * @return HttpResponse             [description]
     */
    public function create3(
        Group $group,
        Dir $dir,
        Create3Load $load,
        Create3Request $request
    ): HttpResponse {
        $dir->setRelations(['group' => $group])
            ->makeService()
            ->createOrUpdateSession($request->validated());

        return Response::view('idir::admin.dir.create.3', App::make(Create3ViewModel::class, [
            'group' => $group
        ]));
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
    ): RedirectResponse {
        $dir->setRelations(['group' => $group])
            ->makeService()
            ->create($request->validated());

        if ($dir->payment instanceof Payment) {
            Event::dispatch(App::make(PaymentStoreEvent::class, ['payment' => $dir->payment]));
        }

        Event::dispatch(App::make(DirStoreEvent::class, ['dir' => $dir]));

        return $response->setDir($dir)->makeResponse();
    }

    /**
     * [edit1 description]
     * @param  Dir       $dir           [description]
     * @param  EditFull1Load $load      [description]
     * @return HttpResponse             [description]
     */
    public function editFull1(Dir $dir, EditFull1Load $load): HttpResponse
    {
        $dir->makeService()->createOrUpdateSession($dir->attributes_as_array);

        return Response::view('idir::admin.dir.edit_full.1', App::make(Edit1ViewModel::class, [
            'dir' => $dir
        ]));
    }

    /**
     * [edit2 description]
     * @param  Dir              $dir     [description]
     * @param  Group            $group   [description]
     * @param  EditFull2Load    $load    [description]
     * @param  EditFull2Request $request [description]
     * @return HttpResponse              [description]
     */
    public function editFull2(Dir $dir, Group $group, EditFull2Load $load, EditFull2Request $request): HttpResponse
    {
        return Response::view('idir::admin.dir.edit_full.2', App::make(EditFull2ViewModel::class, [
            'dir' => $dir,
            'group' => $group
        ]));
    }

    /**
     * [updateFull2 description]
     * @param  Dir              $dir         [description]
     * @param  Group            $group       [description]
     * @param  UpdateFull2Load      $load    [description]
     * @param  UpdateFull2Request   $request [description]
     * @return RedirectResponse              [description]
     */
    public function updateFull2(
        Dir $dir,
        Group $group,
        UpdateFull2Load $load,
        UpdateFull2Request $request
    ): RedirectResponse {
        $dir->setRelations(['group' => $group])
            ->makeService()
            ->createOrUpdateSession($request->validated());

        return Response::redirectToRoute('admin.dir.edit_full_3', [$dir->id, $group->id]);
    }

    /**
     * [editFull3 description]
     * @param  Group              $group    [description]
     * @param  Dir                $dir      [description]
     * @param  EditFull3Load      $load     [description]
     * @param  EditFull3Request   $request  [description]
     * @return HttpResponse                 [description]
     */
    public function editFull3(
        Dir $dir,
        Group $group,
        EditFull3Load $load,
        EditFull3Request $request
    ): HttpResponse {
        $dir->setRelations(['group' => $group])
            ->makeService()
            ->createOrUpdateSession($request->validated());

        return Response::view('idir::admin.dir.edit_full.3', App::make(EditFull3ViewModel::class, [
            'dir' => $dir,
            'group' => $group
        ]));
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
    ): RedirectResponse {
        $dir->setRelations(['group' => $group])
            ->makeService()
            ->updateFull($request->validated());

        if ($dir->payment instanceof Payment) {
            Event::dispatch(App::make(PaymentStoreEvent::class, ['payment' => $dir->payment]));
        }

        Event::dispatch(App::make(DirUpdateFullEvent::class, ['dir' => $dir]));

        return $response->setDir($dir)->makeResponse();
    }

    /**
     * [edit description]
     * @param  Dir          $dir  [description]
     * @param  EditLoad     $load [description]
     * @return JsonResponse       [description]
     */
    public function edit(Dir $dir, EditLoad $load): JsonResponse
    {
        return Response::json([
            'success' => '',
            'view' => View::make('idir::admin.dir.edit', [
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
    public function update(Dir $dir, UpdateRequest $request): JsonResponse
    {
        $dir->setRelations(['group' => $dir->group])
            ->makeService()
            ->update($request->validated());

        return Response::json([
            'success' => '',
            'view' => View::make('idir::admin.dir.partials.dir', [
                'dir' => $dir->loadAllRels()
            ])->render()
        ]);
    }

    /**
     * [updateStatus description]
     * @param  Dir                 $dir     [description]
     * @return JsonResponse                 [description]
     */
    public function updateStatus(Dir $dir, UpdateStatusRequest $request): JsonResponse
    {
        $dir->makeService()->updateStatus($request->only('status'));

        $dir->loadAllRels();

        Event::dispatch(
            App::make(DirUpdateStatusEvent::class, [
                'dir' => $dir,
                'reason' => $request->input('reason')
            ])
        );

        return Response::json([
            'success' => '',
            'status' => $dir->status,
            'view' => View::make('idir::admin.dir.partials.dir', [
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
    public function destroy(Dir $dir, DestroyLoad $load, DestroyRequest $request): JsonResponse
    {
        $dir->makeService()->delete();

        Event::dispatch(
            App::make(DirDestroyEvent::class, [
                'dir' => $dir,
                'reason' => $request->input('reason')
            ])
        );

        return Response::json(['success' => '']);
    }

    /**
     * Remove the collection of Dirs from storage.
     *
     * @param  Dir                  $dir    [description]
     * @param  DestroyGlobalRequest $request [description]
     * @return RedirectResponse              [description]
     */
    public function destroyGlobal(Dir $dir, DestroyGlobalRequest $request): RedirectResponse
    {
        $deleted = $dir->makeService()->deleteGlobal($request->input('select'));

        return Response::redirectTo(URL::previous())->with(
            'success',
            Lang::get('idir::dirs.success.destroy_global', [
                'affected' => $deleted
            ])
        );
    }
}
