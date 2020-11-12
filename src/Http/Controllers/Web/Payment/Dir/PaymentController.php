<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Payment\Dir;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Http\Controllers\Web\Payment\Dir\Polymorphic;
use N1ebieski\IDir\Http\Requests\Web\Payment\Interfaces\CompleteRequestStrategy;
use N1ebieski\IDir\Loads\Web\Payment\ShowLoad;
use N1ebieski\IDir\Utils\Payment\Interfaces\TransferUtilStrategy;

/**
 * [PaymentController description]
 */
class PaymentController extends Controller implements Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Payment $payment
     * @param ShowLoad $load
     * @param TransferUtilStrategy $transferUtil
     * @return RedirectResponse
     */
    public function show(
        Payment $payment,
        ShowLoad $load,
        TransferUtilStrategy $transferUtil
    ) : RedirectResponse {
        try {
            $transferUtil->setup([
                'amount' => $payment->order->price,
                'desc' => Lang::get('idir::payments.dir.desc', [
                    'title' => $payment->morph->title,
                    'group' => $payment->order->group->name,
                    'days' => $days = $payment->order->days,
                    'limit' => $days !== null ?
                        strtolower(Lang::get('idir::groups.days'))
                        : strtolower(Lang::get('idir::groups.unlimited'))
                ]),
                'userdata' => json_encode([
                    'uuid' => $payment->uuid,
                    'redirect' => Auth::check() ?
                        URL::route('web.profile.edit_dir')
                        : URL::route('web.dir.create_1')
                ])
            ])
            ->makeResponse();
        } catch (\N1ebieski\IDir\Exceptions\Payment\Exception $e) {
            throw $e->setPayment($payment);
        }

        return Response::redirectTo($transferUtil->getUrlToPayment());
    }

    /**
     * Undocumented function
     *
     * @param CompleteRequestStrategy $request
     * @param TransferUtilStrategy $transferUtil
     * @return RedirectResponse
     */
    public function complete(
        CompleteRequestStrategy $request,
        TransferUtilStrategy $transferUtil
    ) : RedirectResponse {
        if (!$transferUtil->isSign($request->validated())) {
            App::abort(HttpResponse::HTTP_FORBIDDEN, 'Invalid signature of payment.');
        }

        return Response::redirectTo($request->input('redirect'))->with(
            $request->input('status') === 'ok' ? 'success' : 'danger',
            $request->input('status') === 'ok' ?
                Lang::get('idir::payments.success.complete')
                : Lang::get('idir::payments.error.complete')
        );
    }
}
