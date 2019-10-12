<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Payment\Cashbill\Dir;

use Illuminate\View\View;
use N1ebieski\IDir\Models\Payment\Payment;

/**
 * [PaymentController description]
 */
class PaymentController
{
    /**
     * [show description]
     * @param  Payment $payment [description]
     * @return View           [description]
     */
    public function show(Payment $payment) : View
    {
        $driver = 'cashbill';
        $service = config("services.cashbill.service");
        $transfer_url = config("services.cashbill.transfer_url");
        $key = config("services.cashbill.key");
        $amount = $payment->price->price;
        $desc = $payment->model->title . ' | ' . $payment->price->group->name . ' | ' . $payment->price->days;
        $userdata = $payment->id;
        $sign = md5($service.'|'.$amount.'||'.$desc.'||'.$userdata.'||||||||||||'.$key);

        return view('idir::web.payment.cashbill.dir.show', [
            'payment' => compact('driver', 'service', 'transfer_url', 'key', 'amount', 'desc', 'userdata', 'sign')
        ]);
    }
}
