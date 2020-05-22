<?php

namespace N1ebieski\IDir\Http\Controllers\Api\Payment\Dir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Events\Api\Payment\Dir\VerifyAttemptEvent;
use N1ebieski\IDir\Events\Api\Payment\Dir\VerifySuccessfulEvent;
use N1ebieski\IDir\Http\Controllers\Api\Payment\Dir\Polymorphic;
use N1ebieski\IDir\Http\Requests\Api\Payment\Interfaces\VerifyRequestStrategy;
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
