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

namespace N1ebieski\IDir\Http\Controllers\Api\Dir;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\ICore\Models\User;
use N1ebieski\IDir\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Loads\Api\Dir\StoreLoad;
use N1ebieski\IDir\Loads\Api\Dir\UpdateLoad;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\IDir\Loads\Api\Dir\DestroyLoad;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Filters\Api\Dir\IndexFilter;
use N1ebieski\IDir\Http\Requests\Api\Dir\IndexRequest;
use N1ebieski\IDir\Http\Requests\Api\Dir\StoreRequest;
use N1ebieski\IDir\Http\Requests\Api\Dir\UpdateRequest;
use N1ebieski\IDir\Http\Requests\Api\Dir\DestroyRequest;
use N1ebieski\IDir\Http\Requests\Api\Dir\StoreCodeRequest;
use N1ebieski\IDir\Http\Requests\Api\Dir\UpdateCodeRequest;
use N1ebieski\IDir\Http\Requests\Api\Dir\UpdateStatusRequest;
use N1ebieski\IDir\Events\Api\Dir\IndexEvent as DirIndexEvent;
use N1ebieski\IDir\Events\Api\Dir\StoreEvent as DirStoreEvent;
use N1ebieski\IDir\Events\Api\Dir\UpdateEvent as DirUpdateEvent;
use N1ebieski\IDir\Events\Api\Dir\DestroyEvent as DirDestroyEvent;
use N1ebieski\IDir\Events\Admin\Payment\Dir\StoreEvent as PaymentStoreEvent;
use N1ebieski\IDir\Events\Api\Dir\UpdateStatusEvent as DirUpdateStatusEvent;

/**
 * @group Dirs
 *
 * > Routes:
 *
 *     /routes/vendor/idir/api/dirs.php
 *
 * > Controller:
 *
 *     N1ebieski\IDir\Http\Controllers\Api\Dir\DirController
 *
 * > Resource:
 *
 *     N1ebieski\IDir\Http\Resources\Dir\DirResource
 *
 * Permissions:
 *
 * - api.* - access to all api endpoints
 * - api.dirs.* - access to all dirs endpoints
 * - api.dirs.view - access to endpoints with collection of dirs
 * - api.dirs.create - access to create dir endpoint
 * - api.dirs.edit - access to edit token endpoint
 * - api.dirs.delete - access to delete token endpoint
 */
class DirController
{
    /**
     * Index of dirs
     *
     * <aside class="notice">Available only to users with permissions: api.access and api.dirs.view.</aside>
     *
     * @authenticated
     *
     * @bodyParam filter.status int Must be one of 1 or (available only for admin.dirs.view): 0, 2, 3, 4, 5. Example: 1
     * @bodyParam filter.author int (available only for admin.dirs.view) ID of User relationship. No-example
     * @bodyParam filter.report int (available only for admin.dirs.view) Must be one of 1 or 0. No-example
     *
     * @apiResourceCollection N1ebieski\IDir\Http\Resources\Dir\DirResource
     * @apiResourceModel N1ebieski\IDir\Models\Dir states=titleSentence,contentText,withUser,pending,withCategory,withDefaultGroup with=ratings,categories,group,user
     *
     * @param Dir $dir
     * @param IndexRequest $request
     * @param IndexFilter $filter
     * @return JsonResponse
     */
    public function index(Dir $dir, IndexRequest $request, IndexFilter $filter): JsonResponse
    {
        $dirs = $dir->makeCache()->rememberByFilter($filter->all());

        Event::dispatch(App::make(DirIndexEvent::class, ['dirs' => $dirs]));

        /** @var Category|null */
        $category = $filter->get('category');

        /** @var Group|null */
        $group = $filter->get('group');

        /** @var User|null */
        $author = $filter->get('author');

        return $dir->makeResource()
            ->collection($dirs)
            ->additional(['meta' => [
                'filter' => Collect::make($filter->all())
                    ->replace([
                        'category' => $category instanceof Category ?
                            $category->makeResource()
                            : $category,
                        'group' => $group instanceof Group ?
                            $group->makeResource()
                            : $group,
                        'author' => $author instanceof User ?
                            $author->makeResource()
                            : $author
                    ])
                    ->toArray()
            ]])
            ->response();
    }

    /**
     * Create dir
     *
     * <aside class="notice">If the user is authenticated the entry is added to his/her account, if not, an account is created for the email address provided.</aside>
     *
     * @urlParam group_id integer required The ID of the group. Example: 1
     *
     * @bodyParam field object[] Array containing additional form fields if the group allows it. No-example
     * @bodyParam url string Unique website url with http/https protocol. Example: https://demo.idir.intelekt.net.pl
     * @bodyParam backlink integer ID of the selected backlink. No-example
     * @bodyParam backlink_url string Url with http/https protocol to backlink. No-example
     * @bodyParam email string Email address, required if the user adds the entry as unauthenticated. Example: kontakt@intelekt.net.pl
     * @bodyParam payment_type string If the group requires a payment, the selected type must be defined. Must be one of <code>transfer</code>, <code>code_transfer</code>, <code>code_sms</code>, or <code>paypal_express</code>. No-example
     * @bodyParam payment_transfer integer ID of the selected Price if payment_type is <code>transfer</code>. No-example
     * @bodyParam payment_code_sms integer ID of the selected Price if payment_type is <code>code_sms</code>. No-example
     * @bodyParam code_sms string Received code if payment_type is <code>code_sms</code>. No-example
     * @bodyParam payment_code_transfer integer ID of the selected Price if payment_type is <code>code_transfer</code>. No-example
     * @bodyParam code_transfer string Received code if payment_type is <code>code_transfer</code>. No-example
     * @bodyParam payment_paypal_express integer ID of the selected Price if payment_type is <code>paypal_express</code>. No-example
     *
     * @apiResource 201 N1ebieski\IDir\Http\Resources\Dir\DirResource
     * @apiResourceModel N1ebieski\IDir\Models\Dir states=titleSentence,contentText,withUser,pending,withCategory,withDefaultGroup with=ratings,categories,group,user
     *
     * @param Group $group
     * @param Dir $dir
     * @param StoreLoad $load
     * @param StoreRequest $request
     * @param StoreCodeRequest $requestPayment
     * @return JsonResponse
     */
    public function store(
        Group $group,
        Dir $dir,
        StoreLoad $load,
        StoreRequest $request,
        StoreCodeRequest $requestPayment
    ): JsonResponse {
        $dir = $dir->makeService()->create(
            $request->safe()->merge([
                'user' => $request->user(),
                'group' => $group
            ])->toArray()
        );

        if ($dir->payment instanceof Payment) {
            Event::dispatch(App::make(PaymentStoreEvent::class, ['payment' => $dir->payment]));
        }

        Event::dispatch(App::make(DirStoreEvent::class, ['dir' => $dir]));

        $dir->loadAllRels();

        return $dir->makeResource()
            ->response()
            ->setStatusCode(HttpResponse::HTTP_CREATED);
    }

    /**
     * Edit dir
     *
     * <aside class="notice">Available only to users with permissions: api.access and api.dirs.edit.</aside>
     *
     * @authenticated
     *
     * @urlParam dir_id integer required The ID of the dir. No-example
     * @urlParam group_id integer required The ID of the group. If same as current and has active premium time then no new payment is required. Example: 1
     *
     * @bodyParam field object[] Array containing additional form fields if the group allows it. No-example
     * @bodyParam url string Unique website url with http/https protocol. Example: https://demo.idir.intelekt.net.pl
     * @bodyParam backlink integer ID of the selected backlink. No-example
     * @bodyParam backlink_url string Url with http/https protocol to backlink. No-example
     * @bodyParam user integer (available only for admin.dirs.edit) ID of User author. If null is no author. No-example
     * @bodyParam payment_type string If the group requires a payment, the selected type must be defined. Must be one of <code>transfer</code>, <code>code_transfer</code>, <code>code_sms</code>, or <code>paypal_express</code>. No-example
     * @bodyParam payment_transfer integer ID of the selected Price if payment_type is <code>transfer</code>. No-example
     * @bodyParam payment_code_sms integer ID of the selected Price if payment_type is <code>code_sms</code>. No-example
     * @bodyParam code_sms string Received code if payment_type is <code>code_sms</code>. No-example
     * @bodyParam payment_code_transfer integer ID of the selected Price if payment_type is <code>code_transfer</code>. No-example
     * @bodyParam code_transfer string Received code if payment_type is <code>code_transfer</code>. No-example
     * @bodyParam payment_paypal_express integer ID of the selected Price if payment_type is <code>paypal_express</code>. No-example
     *
     * @apiResource N1ebieski\IDir\Http\Resources\Dir\DirResource
     * @apiResourceModel N1ebieski\IDir\Models\Dir states=titleSentence,contentText,withUser,pending,withCategory,withDefaultGroup with=ratings,categories,group,user
     *
     * @param Dir $dir
     * @param Group $group
     * @param UpdateLoad $load
     * @param UpdateRequest $request
     * @param UpdateCodeRequest $requestPayment
     * @return JsonResponse
     */
    public function update(
        Dir $dir,
        Group $group,
        UpdateLoad $load,
        UpdateRequest $request,
        UpdateCodeRequest $requestPayment
    ): JsonResponse {
        $dir->makeService()->update(
            $request->safe()->merge(['group' => $group])->toArray()
        );

        if ($dir->payment instanceof Payment) {
            Event::dispatch(App::make(PaymentStoreEvent::class, ['payment' => $dir->payment]));
        }

        Event::dispatch(App::make(DirUpdateEvent::class, ['dir' => $dir]));

        $dir->loadAllRels();

        return $dir->makeResource()->response();
    }

    /**
     * Edit status dir
     *
     * <aside class="notice">Available only to users with permissions: api.access, api.dirs.status and admin.dirs.status.</aside>
     *
     * @authenticated
     *
     * @urlParam dir_id integer required The ID of the dir. No-example
     *
     * @apiResource N1ebieski\IDir\Http\Resources\Dir\DirResource
     * @apiResourceModel N1ebieski\IDir\Models\Dir states=titleSentence,contentText,withUser,pending,withCategory,withDefaultGroup with=ratings,categories,group,user
     *
     * @param  Dir                 $dir     [description]
     * @return JsonResponse                 [description]
     */
    public function updateStatus(Dir $dir, UpdateStatusRequest $request): JsonResponse
    {
        $dir->makeService()->updateStatus($request->input('status'));

        $dir->loadAllRels();

        Event::dispatch(
            App::make(DirUpdateStatusEvent::class, [
                'dir' => $dir,
                'reason' => $request->validated()['reason'] ?? null
            ])
        );

        return $dir->makeResource()->response();
    }

    /**
     * Delete dir
     *
     * <aside class="notice">Available only to users with permissions: api.access and api.dirs.delete.</aside>
     *
     * @authenticated
     *
     * @urlParam dir_id integer required The ID of the dir. No-example
     *
     * @bodyParam reason string (available only for admin.dirs.delete). No-example
     *
     * @response 204
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
                'reason' => $request->validated()['reason'] ?? null
            ])
        );

        return Response::json([], HttpResponse::HTTP_NO_CONTENT);
    }
}
