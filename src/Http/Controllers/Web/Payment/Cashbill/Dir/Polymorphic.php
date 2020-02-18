<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Payment\Cashbill\Dir;

use Illuminate\Http\RedirectResponse;
use N1ebieski\IDir\Http\Requests\Web\Payment\Cashbill\Dir\CompleteRequest;
use N1ebieski\IDir\Http\Requests\Web\Payment\Cashbill\Dir\VerifyRequest;
use N1ebieski\IDir\Http\Requests\Web\Payment\Cashbill\Dir\ShowRequest;
use N1ebieski\IDir\Utils\Cashbill\Transfer as Cashbill;
use N1ebieski\IDir\Models\Payment\Dir\Payment;

/**
 * [interface description]
 */
interface Polymorphic
{
    /**
     * [show description]
     * @param  Payment  $payment  [description]
     * @param  ShowRequest $request [description]
     * @param  Cashbill $cashbill [description]
     * @return RedirectResponse               [description]
     */
    public function show(Payment $payment, ShowRequest $request, Cashbill $cashbill) : RedirectResponse;

    /**
     * [complete description]
     * @param  CompleteRequest  $request  [description]
     * @param  Cashbill         $cashbill [description]
     * @return RedirectResponse           [description]
     */
    public function complete(CompleteRequest $request, Cashbill $cashbill) : RedirectResponse;

    /**
     * [verify description]
     * @param  Payment       $payment  [description]
     * @param  VerifyRequest $request  [description]
     * @param  Cashbill      $cashbill [description]
     * @return string                  [description]
     */
    public function verify(Payment $payment, VerifyRequest $request, Cashbill $cashbill) : string;
}
