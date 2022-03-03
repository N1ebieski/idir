<?php

namespace N1ebieski\IDir\Http\Controllers\Api\Dir;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use N1ebieski\IDir\Loads\Api\Dir\StoreLoad;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Http\Resources\Dir\DirResource;
use N1ebieski\IDir\Http\Requests\Api\Dir\StoreRequest;
use N1ebieski\IDir\Http\Requests\Api\Dir\StoreCodeRequest;
use N1ebieski\IDir\Events\Api\Dir\StoreEvent as DirStoreEvent;
use N1ebieski\IDir\Events\Admin\Payment\Dir\StoreEvent as PaymentStoreEvent;

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
 *     N1ebieski\ICore\Http\Resources\Dir\DirResource
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
     * Create dir
     *
     * <aside class="notice">If the user is authenticated the entry is added to his/her account, if not, an account is created for the email address provided.</aside>
     *
     * @urlParam group integer required The group ID. Example: 1
     *
     * @bodyParam field object[] Array containing additional form fields if the group allows it. No-example
     * @bodyParam url string Unique website url with http/https protocol. Example: https://demo.idir.intelekt.net.pl
     * @bodyParam backlink integer ID of the selected backlink. No-example
     * @bodyParam backlink_url string Url with http/https protocol to backlink. No-example
     * @bodyParam email string Email address, required if the user adds the entry as unauthenticated. Example: kontakt@intelekt.net.pl
     * @bodyParam payment_type string If the group requires a payment, the selected type must be defined. Must be one of <code>transfer</code>, <code>code_transfer</code>, <code>code_sms</code>, or <code>paypal_express</code>. No-example
     * @bodyParam payment_transfer integer ID of the selected price if payment_type is <code>transfer</code>. No-example
     * @bodyParam payment_code_sms integer ID of the selected price if payment_type is <code>code_sms</code>. No-example
     * @bodyParam code_sms string Received code if payment_type is <code>code_sms</code>. No-example
     * @bodyParam payment_code_transfer integer ID of the selected price if payment_type is <code>code_transfer</code>. No-example
     * @bodyParam code_transfer string Received code if payment_type is <code>code_transfer</code>. No-example
     * @bodyParam payment_paypal_express integer ID of the selected price if payment_type is <code>paypal_express</code>. No-example
     *
     * @apiResource N1ebieski\IDir\Http\Resources\Dir\DirResource
     * @apiResourceModel N1ebieski\IDir\Models\Dir states=title_sentence,content_text,with_user,pending,with_category,with_default_group with=ratings,categories,group,user
     * @apiResourceAdditional payment_url="https://demo.idir.intelekt.net.pl/api/payments/1a7005ff-8db3-4e47-8f21-a4c2a333e395"
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
        $dir = $dir->setRelations(['group' => $group])
            ->makeService()
            ->create($request->validated());

        if ($dir->payment instanceof Payment) {
            Event::dispatch(App::make(PaymentStoreEvent::class, ['payment' => $dir->payment]));
        }

        Event::dispatch(App::make(DirStoreEvent::class, ['dir' => $dir]));

        return App::make(DirResource::class, ['dir' => $dir->loadAllRels()])
            ->response()
            ->setStatusCode(HttpResponse::HTTP_CREATED);
    }

    // public function update(Dir $dir)
    // {
    //     dd($dir);
    // }
}
