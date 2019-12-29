<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Payment\Cashbill\Dir;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use N1ebieski\IDir\Http\Requests\Web\Payment\Cashbill\Dir\CompleteRequest;
use N1ebieski\IDir\Http\Requests\Web\Payment\Cashbill\Dir\VerifyRequest;
use N1ebieski\IDir\Http\Requests\Web\Payment\Cashbill\Dir\ShowRequest;
use N1ebieski\IDir\Utils\Cashbill\Transfer as Cashbill;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Events\Web\Payment\Dir\VerifyAttempt;
use N1ebieski\IDir\Events\Web\Payment\Dir\VerifySuccessful;

/**
 * [PaymentController description]
 */
class PaymentController
{
    /**
     * [show description]
     * @param  Payment  $payment  [description]
     * @param  ShowRequest $request [description]
     * @param  Cashbill $cashbill [description]
     * @return View               [description]
     */
    public function show(Payment $payment, ShowRequest $request, Cashbill $cashbill) : View
    {
        $payment->load(['morph', 'price_morph']);

        return view('idir::web.payment.cashbill.dir.show', [
            'payment' => $cashbill->setup([
                    'amount' => $payment->price->price,
                    'desc' => trans('idir::payments.desc.dir', [
                        'title' => $payment->morph->title,
                        'group' => $payment->price->group->name,
                        'days' => $days = $payment->price->days,
                        'limit' => $days !== null ? strtolower(trans('idir::groups.days'))
                            : strtolower(trans('idir::groups.unlimited'))
                    ]),
                    'userdata' => json_encode([
                        'id' => $payment->id,
                        'redirect' => $request->input('redirect')
                    ])
                ])->all()
        ]);
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
            abort(403, 'Invalid signature of payment.');
        }

        return redirect()->route($request->input('redirect'))->with(
                $request->input('status') === 'ok' ? 'success' : 'danger',
                $request->input('status') === 'ok' ? trans('idir::payments.success.complete')
                    : trans('idir::payments.error.complete')
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
        ($payment = $payment->makeRepo()->firstPendingById($request->input('id'))) ?? abort(404);

        event(new VerifyAttempt($payment));

        try {
            $cashbill->setup(['amount' => $payment->price->price])->verify($request->validated());
        } catch (\N1ebieski\IDir\Exceptions\Cashbill\Exception $e) {
            throw $e->setPayment($payment);
        }

        $payment->makeRepo()->paid();

        event(new VerifySuccessful($payment));

        return 'OK';
    }
}
