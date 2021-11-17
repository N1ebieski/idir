<?php

namespace N1ebieski\IDir\Loads\Web\Payment\Dir;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Models\Payment\Dir\Payment;

class CompleteLoad
{
    /**
     * Undocumented variable
     *
     * @var Payment
     */
    protected $payment;

    /**
     * Undocumented function
     *
     * @param Payment $payment
     * @param Request $request
     */
    public function __construct(Payment $payment, Request $request)
    {
        $this->payment = $payment->makeRepo()->firstByUuid($request->input('uuid'));

        if ($this->payment === null) {
            App::abort(HttpResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * Undocumented function
     *
     * @return Payment
     */
    public function getPayment(): Payment
    {
        return $this->payment;
    }
}
