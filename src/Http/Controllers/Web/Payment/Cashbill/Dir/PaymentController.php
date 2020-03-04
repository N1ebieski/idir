<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Payment\Cashbill\Dir;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Utils\Cashbill\TransferUtil as Cashbill;
use N1ebieski\IDir\Events\Web\Payment\Dir\VerifyAttemptEvent;
use N1ebieski\IDir\Events\Web\Payment\Dir\VerifySuccessfulEvent;
use N1ebieski\IDir\Http\Requests\Web\Payment\Cashbill\Dir\ShowRequest;
use N1ebieski\IDir\Http\Requests\Web\Payment\Cashbill\Dir\VerifyRequest;
use N1ebieski\IDir\Http\Controllers\Web\Payment\Cashbill\Dir\Polymorphic;
use N1ebieski\IDir\Http\Requests\Web\Payment\Cashbill\Dir\CompleteRequest;

/**
 * [PaymentController description]
 */
class PaymentController implements Polymorphic
{
    /**
     * [show description]
     * @param  Payment  $payment  [description]
     * @param  ShowRequest $request [description]
     * @param  Cashbill $cashbill [description]
     * @return RedirectResponse               [description]
     */
    public function show(Payment $payment, ShowRequest $request, Cashbill $cashbill) : RedirectResponse
    {
        $payment->load(['morph', 'priceMorph']);

        try {
            $response = $cashbill->setup([
                'amount' => $payment->price->price,
                'desc' => trans('idir::payments.desc.dir', [
                    'title' => $payment->morph->title,
                    'group' => $payment->price->group->name,
                    'days' => $days = $payment->price->days,
                    'limit' => $days !== null ? strtolower(trans('idir::groups.days'))
                        : strtolower(trans('idir::groups.unlimited'))
                ]),
                'userdata' => json_encode([
                    'uuid' => $payment->uuid,
                    'redirect' => $request->input('redirect')
                ])
            ])
            ->response();
        } catch (\N1ebieski\IDir\Exceptions\Cashbill\Exception $e) {
            throw $e->setPayment($payment);
        }

        $redirects = $response->getHeader(\GuzzleHttp\RedirectMiddleware::HISTORY_HEADER);

        return Response::redirectTo(end($redirects));
    }

    /**
     * [complete description]
     * @param  CompleteRequest  $request  [description]
     * @param  Cashbill         $cashbill [description]
     * @return RedirectResponse           [description]
     */
    public function complete(CompleteRequest $request, Cashbill $cashbill) : RedirectResponse
    {
        if (!$cashbill->isSign($request->validated())) {
            App::abort(403, 'Invalid signature of payment.');
        }

        return Response::redirectTo($request->input('redirect'))->with(
            $request->input('status') === 'ok' ? 'success' : 'danger',
            $request->input('status') === 'ok' ?
                Lang::get('idir::payments.success.complete')
                : Lang::get('idir::payments.error.complete')
        );
    }

    /**
     * [verify description]
     * @param  Payment       $payment  [description]
     * @param  VerifyRequest $request  [description]
     * @param  Cashbill      $cashbill [description]
     * @return string                  [description]
     */
    public function verify(Payment $payment, VerifyRequest $request, Cashbill $cashbill) : string
    {
        // I can't' use the route binding, because cashbill returns id payment as part of $_POST['userdata'] variable
        ($payment = $payment->makeRepo()->firstPendingByUuid($request->input('uuid'))) ?? App::abort(404);

        Event::dispatch(App::make(VerifyAttemptEvent::class, ['payment' => $payment]));

        try {
            $cashbill->setup(['amount' => $payment->price->price])->authorize($request->validated());
        } catch (\N1ebieski\IDir\Exceptions\Cashbill\Exception $e) {
            throw $e->setPayment($payment);
        }

        $payment->makeRepo()->paid();

        Event::dispatch(App::make(VerifySuccessfulEvent::class, ['payment' => $payment]));

        return 'OK';
    }
}
