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

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Loads\Web\Dir\ShowLoad;
use N1ebieski\IDir\Loads\Web\Dir\Edit1Load;
use N1ebieski\IDir\Loads\Web\Dir\Edit2Load;
use N1ebieski\IDir\Loads\Web\Dir\Edit3Load;
use N1ebieski\IDir\Loads\Web\Dir\Store2Load;
use N1ebieski\IDir\Loads\Web\Dir\Store3Load;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Loads\Web\Dir\Create2Load;
use N1ebieski\IDir\Loads\Web\Dir\Create3Load;
use N1ebieski\IDir\Loads\Web\Dir\DestroyLoad;
use N1ebieski\IDir\Loads\Web\Dir\Update2Load;
use N1ebieski\IDir\Loads\Web\Dir\Update3Load;
use N1ebieski\IDir\Filters\Web\Dir\ShowFilter;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Filters\Web\Dir\IndexFilter;
use N1ebieski\IDir\Loads\Web\Dir\EditRenewLoad;
use N1ebieski\IDir\Filters\Web\Dir\SearchFilter;
use N1ebieski\IDir\Loads\Web\Dir\UpdateRenewLoad;
use Illuminate\Database\ClassMorphViolationException;
use N1ebieski\IDir\Http\Requests\Web\Dir\ShowRequest;
use Illuminate\Database\Eloquent\InvalidCastException;
use N1ebieski\IDir\Http\Requests\Web\Dir\Edit2Request;
use N1ebieski\IDir\Http\Requests\Web\Dir\Edit3Request;
use N1ebieski\IDir\Http\Requests\Web\Dir\IndexRequest;
use N1ebieski\IDir\Http\Requests\Web\Dir\SearchRequest;
use N1ebieski\IDir\Http\Requests\Web\Dir\Store2Request;
use N1ebieski\IDir\Http\Requests\Web\Dir\Store3Request;
use N1ebieski\IDir\Services\Payment\Dir\PaymentFactory;
use N1ebieski\IDir\Http\Requests\Web\Dir\Create2Request;
use N1ebieski\IDir\Http\Requests\Web\Dir\Create3Request;
use N1ebieski\IDir\Http\Requests\Web\Dir\Update2Request;
use N1ebieski\IDir\Http\Requests\Web\Dir\Update3Request;
use N1ebieski\IDir\Http\Responses\Web\Dir\Store3Response;
use N1ebieski\IDir\View\ViewModels\Web\Dir\ShowViewModel;
use N1ebieski\IDir\Http\Requests\Web\Dir\EditRenewRequest;
use N1ebieski\IDir\Http\Responses\Web\Dir\Update3Response;
use N1ebieski\IDir\View\ViewModels\Web\Dir\Edit1ViewModel;
use N1ebieski\IDir\View\ViewModels\Web\Dir\Edit2ViewModel;
use N1ebieski\IDir\View\ViewModels\Web\Dir\Edit3ViewModel;
use N1ebieski\IDir\Http\Requests\Web\Dir\Store3CodeRequest;
use N1ebieski\IDir\Events\Web\Dir\ShowEvent as DirShowEvent;
use N1ebieski\IDir\Http\Requests\Web\Dir\Update3CodeRequest;
use N1ebieski\IDir\Http\Requests\Web\Dir\UpdateRenewRequest;
use N1ebieski\IDir\View\ViewModels\Web\Dir\Create1ViewModel;
use N1ebieski\IDir\View\ViewModels\Web\Dir\Create2ViewModel;
use N1ebieski\IDir\View\ViewModels\Web\Dir\Create3ViewModel;
use N1ebieski\IDir\Events\Web\Dir\IndexEvent as DirIndexEvent;
use N1ebieski\IDir\Events\Web\Dir\StoreEvent as DirStoreEvent;
use N1ebieski\IDir\Http\Responses\Web\Dir\UpdateRenewResponse;
use N1ebieski\IDir\View\ViewModels\Web\Dir\EditRenewViewModel;
use N1ebieski\IDir\Events\Web\Dir\SearchEvent as DirSearchEvent;
use N1ebieski\IDir\Events\Web\Dir\UpdateEvent as DirUpdateEvent;
use N1ebieski\IDir\Http\Requests\Web\Dir\UpdateRenewCodeRequest;
use N1ebieski\IDir\Events\Web\Dir\DestroyEvent as DirDestroyEvent;
use N1ebieski\IDir\Events\Web\Dir\UpdateRenewEvent as DirUpdateRenewEvent;
use N1ebieski\IDir\Events\Web\Payment\Dir\StoreEvent as PaymentStoreEvent;

class DirController
{
    /**
     *
     * @param Dir $dir
     * @param IndexRequest $request
     * @param IndexFilter $filter
     * @return HttpResponse
     */
    public function index(Dir $dir, IndexRequest $request, IndexFilter $filter): HttpResponse
    {
        $dirs = $dir->makeCache()->rememberForWebByFilter($filter->all());

        Event::dispatch(App::make(DirIndexEvent::class, ['dirs' => $dirs]));

        return Response::view('idir::web.dir.index', [
            'dirs' => $dirs,
            'filter' => $filter->all()
        ]);
    }

    /**
     * [search description]
     * @param  Dir           $dir    [description]
     * @param  SearchRequest $request [description]
     * @return HttpResponse                   [description]
     */
    public function search(Dir $dir, SearchRequest $request, SearchFilter $filter): HttpResponse
    {
        $dirs = $dir->makeRepo()->paginateBySearchAndFilter(
            $request->input('search'),
            $filter->all()
        );

        Event::dispatch(App::make(DirSearchEvent::class, ['dirs' => $dirs]));

        return Response::view('idir::web.dir.search', [
            'dirs' => $dirs,
            'filter' => $filter->all(),
            'search' => $request->input('search')
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param ShowLoad $load
     * @param ShowRequest $request
     * @param ShowFilter $filter
     * @return HttpResponse
     */
    public function show(
        Dir $dir,
        ShowLoad $load,
        ShowRequest $request,
        ShowFilter $filter
    ): HttpResponse {
        Event::dispatch(App::make(DirShowEvent::class, ['dir' => $dir]));

        return Response::view('idir::web.dir.show', App::make(ShowViewModel::class, [
            'dir' => $dir,
            'filter' => $filter
        ]));
    }

    /**
     * [create1 description]
     * @return HttpResponse
     */
    public function create1(): HttpResponse
    {
        return Response::view('idir::web.dir.create.1', App::make(Create1ViewModel::class));
    }

    /**
     * [create2 description]
     * @param  Group          $group   [description]
     * @param  Create2Load    $load    [description]
     * @param  Create2Request $request [description]
     * @return HttpResponse            [description]
     */
    public function create2(Group $group, Create2Load $load, Create2Request $request): HttpResponse
    {
        return Response::view('idir::web.dir.create.2', App::make(Create2ViewModel::class, [
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
        $dir->makeService()->createOrUpdateSession($request->validated());

        return Response::redirectToRoute('web.dir.create_3', [$group->id]);
    }

    /**
     * [create3 description]
     * @param  Group          $group    [description]
     * @param  Dir            $dir      [description]
     * @param  Create3Load    $load     [description]
     * @param  Create3Request $request  [description]
     * @return HttpResponse                     [description]
     */
    public function create3(
        Group $group,
        Dir $dir,
        Create3Load $load,
        Create3Request $request
    ): HttpResponse {
        $dir->makeService()->createOrUpdateSession($request->validated());

        return Response::view('idir::web.dir.create.3', App::make(Create3ViewModel::class, [
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
        $dir->makeService()->create(
            $request->safe()->merge([
                'user' => $request->user(),
                'group' => $group
            ])->toArray()
        );

        if ($dir->payment instanceof Payment) {
            Event::dispatch(App::make(PaymentStoreEvent::class, ['payment' => $dir->payment]));
        }

        Event::dispatch(App::make(DirStoreEvent::class, ['dir' => $dir]));

        return $response->makeResponse($dir);
    }

    /**
     * [edit1 description]
     * @param  Dir       $dir   [description]
     * @param  Edit1Load $load  [description]
     * @return HttpResponse             [description]
     */
    public function edit1(Dir $dir, Edit1Load $load): HttpResponse
    {
        $dir->makeService()->createOrUpdateSession($dir->attributes_as_array);

        return Response::view('idir::web.dir.edit.1', App::make(Edit1ViewModel::class, [
            'dir' => $dir
        ]));
    }

    /**
     * [edit2 description]
     * @param  Dir          $dir     [description]
     * @param  Group        $group   [description]
     * @param  Edit2Load    $load    [description]
     * @param  Edit2Request $request [description]
     * @return HttpResponse                  [description]
     */
    public function edit2(Dir $dir, Group $group, Edit2Load $load, Edit2Request $request): HttpResponse
    {
        return Response::view('idir::web.dir.edit.2', App::make(Edit2ViewModel::class, [
            'dir' => $dir,
            'group' => $group
        ]));
    }

    /**
     * [update2 description]
     * @param  Dir              $dir     [description]
     * @param  Group            $group   [description]
     * @param  Update2Load      $load    [description]
     * @param  Update2Request   $request [description]
     * @return RedirectResponse          [description]
     */
    public function update2(Dir $dir, Group $group, Update2Load $load, Update2Request $request): RedirectResponse
    {
        $dir->makeService()->createOrUpdateSession($request->validated());

        return Response::redirectToRoute('web.dir.edit_3', [$dir->id, $group->id]);
    }

    /**
     * [edit3 description]
     * @param  Group          $group    [description]
     * @param  Dir            $dir      [description]
     * @param  Edit3Load      $load     [description]
     * @param  Edit3Request   $request  [description]
     * @return HttpResponse                     [description]
     */
    public function edit3(
        Dir $dir,
        Group $group,
        Edit3Load $load,
        Edit3Request $request
    ): HttpResponse {
        $dir->makeService()->createOrUpdateSession($request->validated());

        return Response::view('idir::web.dir.edit.3', App::make(Edit3ViewModel::class, [
            'dir' => $dir,
            'group' => $group
        ]));
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
    ): RedirectResponse {
        $dir->makeService()->updateFull(
            $request->safe()->merge(['group' => $group])->toArray()
        );

        if ($dir->payment instanceof Payment) {
            Event::dispatch(App::make(PaymentStoreEvent::class, ['payment' => $dir->payment]));
        }

        Event::dispatch(App::make(DirUpdateEvent::class, ['dir' => $dir]));

        return $response->makeResponse($dir);
    }

    /**
     * [editRenew description]
     * @param  Dir              $dir     [description]
     * @param  EditRenewLoad    $load    [description]
     * @param  EditRenewRequest $request [description]
     * @return HttpResponse                      [description]
     */
    public function editRenew(Dir $dir, EditRenewLoad $load, EditRenewRequest $request): HttpResponse
    {
        return Response::view('idir::web.dir.edit_renew', App::make(EditRenewViewModel::class, [
            'dir' => $dir
        ]));
    }

    /**
     *
     * @param Dir $dir
     * @param UpdateRenewLoad $load
     * @param UpdateRenewRequest $request
     * @param UpdateRenewCodeRequest $requestPayment
     * @param PaymentFactory $paymentFactory
     * @param UpdateRenewResponse $response
     * @return RedirectResponse
     * @throws InvalidCastException
     * @throws ClassMorphViolationException
     */
    public function updateRenew(
        Dir $dir,
        UpdateRenewLoad $load,
        UpdateRenewRequest $request,
        UpdateRenewCodeRequest $requestPayment,
        PaymentFactory $paymentFactory,
        UpdateRenewResponse $response
    ): RedirectResponse {
        $dir->setRelations([
            'payment' => $payment = $paymentFactory->makePayment(
                $dir,
                $request->validated()['price']
            )
        ]);

        Event::dispatch(App::make(PaymentStoreEvent::class, ['payment' => $payment]));
        Event::dispatch(App::make(DirUpdateRenewEvent::class, ['dir' => $dir]));

        return $response->makeResponse($dir);
    }

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param DestroyLoad $load
     * @return JsonResponse
     */
    public function destroy(Dir $dir, DestroyLoad $load): JsonResponse
    {
        $dir->makeService()->delete();

        Event::dispatch(App::make(DirDestroyEvent::class, ['dir' => $dir]));

        return Response::json([]);
    }
}
