<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Payment\Dir;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Events\Web\Payment\Dir\VerifyAttemptEvent;
use N1ebieski\IDir\Events\Web\Payment\Dir\VerifySuccessfulEvent;
use N1ebieski\IDir\Http\Controllers\Web\Payment\Dir\Polymorphic;
use N1ebieski\IDir\Http\Requests\Web\Payment\Interfaces\CompleteRequestStrategy;
use N1ebieski\IDir\Http\Requests\Web\Payment\Interfaces\ShowRequestStrategy;
use N1ebieski\IDir\Http\Requests\Web\Payment\Interfaces\VerifyRequestStrategy;
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
     * @param ShowRequestStrategy $request
     * @param TransferUtilStrategy $transferUtil
     * @return RedirectResponse
     */
    public function show(
        Payment $payment,
        ShowLoad $load,
        ShowRequestStrategy $request,
        TransferUtilStrategy $transferUtil
    ) : RedirectResponse {
        try {
            $transferUtil->setup([
                'amount' => $payment->order->price,
                'desc' => trans('idir::payments.dir.desc', [
                    'title' => $payment->morph->title,
                    'group' => $payment->order->group->name,
                    'days' => $days = $payment->order->days,
                    'limit' => $days !== null ? strtolower(trans('idir::groups.days'))
                        : strtolower(trans('idir::groups.unlimited'))
                ]),
                'userdata' => json_encode([
                    'uuid' => $payment->uuid,
                    'redirect' => $request->input('redirect')
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

    /**
     * Undocumented function
     *
     * @param Payment $payment
     * @param VerifyRequestStrategy $request
     * @param TransferUtilStrategy $transferUtil
     * @return string
     */
    public function verify(
        Payment $payment,
        VerifyRequestStrategy $request,
        TransferUtilStrategy $transferUtil
    ) : string {
        // I can't' use the route binding, because cashbill returns id payment as part of $_POST['userdata'] variable
        $payment = $payment->makeRepo()->firstPendingByUuid($request->input('uuid'));

        if ($payment === null) {
            App::abort(HttpResponse::HTTP_NOT_FOUND);
        }

        Event::dispatch(App::make(VerifyAttemptEvent::class, ['payment' => $payment]));

        try {
            $transferUtil->setup(['amount' => $payment->order->price])->authorize($request->validated());
        } catch (\N1ebieski\IDir\Exceptions\Payment\Exception $e) {
            throw $e->setPayment($payment);
        }

        $payment->makeRepo()->paid();
        
        Event::dispatch(App::make(VerifySuccessfulEvent::class, ['payment' => $payment]));

        return 'OK';
    }
}
