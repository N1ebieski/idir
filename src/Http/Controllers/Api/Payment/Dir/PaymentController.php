<?php

namespace N1ebieski\IDir\Http\Controllers\Api\Payment\Dir;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Event;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Loads\Api\Payment\Dir\ShowLoad;
use N1ebieski\IDir\Loads\Api\Payment\Dir\VerifyLoad;
use N1ebieski\IDir\Events\Api\Payment\Dir\VerifyAttemptEvent;
use N1ebieski\IDir\Http\Resources\Payment\Dir\PaymentResource;
use N1ebieski\IDir\Events\Api\Payment\Dir\VerifySuccessfulEvent;
use N1ebieski\IDir\Http\Controllers\Api\Payment\Dir\Polymorphic;
use N1ebieski\IDir\Utils\Payment\Interfaces\TransferUtilStrategy;
use N1ebieski\IDir\Http\Requests\Api\Payment\Interfaces\VerifyRequestStrategy;

/**
 * @group Payments
 */
class PaymentController extends Controller implements Polymorphic
{
    /**
     * Initialise dir's payment
     *
     * @urlParam payment_dir_pending string required The payment UUID. No-example
     * @urlParam driver string The payment driver. No-example
     *
     * @param Payment $payment
     * @param string $driver
     * @param ShowLoad $load
     * @param TransferUtilStrategy $transferUtil
     * @return JsonResponse
     */
    public function show(
        Payment $payment,
        string $driver = null,
        ShowLoad $load,
        TransferUtilStrategy $transferUtil
    ): JsonResponse {
        try {
            $transferUtil->setAmount($payment->order->price)
                ->setDesc(
                    Lang::get('idir::payments.dir.desc', [
                        'title' => $payment->morph->title,
                        'group' => $payment->order->group->name,
                        'days' => $days = $payment->order->days,
                        'limit' => $days !== null ?
                            strtolower(Lang::get('idir::prices.days'))
                            : strtolower(Lang::get('idir::prices.unlimited'))
                    ])
                )
                ->setUuid($payment->uuid)
                ->setRedirect('')
                ->setNotifyUrl(URL::route('api.payment.dir.verify', [$driver]))
                ->setReturnUrl(URL::route('api.payment.dir.complete', [$driver]))
                ->setCancelUrl(URL::route('api.payment.dir.complete', [$driver]))
                ->purchase();
        } catch (\N1ebieski\IDir\Exceptions\Payment\Exception $e) {
            throw $e->setPayment($payment);
        }

        return App::make(PaymentResource::class, ['payment' => $payment])
            ->additional(['url' => $transferUtil->getUrlToPayment()])
            ->response();
    }

    /**
     * @hideFromAPIDocumentation
     *
     * @param string $driver
     * @param VerifyRequestStrategy $request
     * @param VerifyLoad $load
     * @param TransferUtilStrategy $transferUtil
     * @return string
     */
    public function verify(
        string $driver = null,
        VerifyRequestStrategy $request,
        VerifyLoad $load,
        TransferUtilStrategy $transferUtil
    ): string {
        try {
            Event::dispatch(App::make(VerifyAttemptEvent::class, ['payment' => $load->getPayment()]));

            $transferUtil->setAmount($load->getPayment()->order->price)
                ->authorize($request->all());

            $load->getPayment()->makeRepo()->paid();

            Event::dispatch(App::make(VerifySuccessfulEvent::class, ['payment' => $load->getPayment()]));
        } catch (\N1ebieski\IDir\Exceptions\Payment\Exception $e) {
            $e->setPayment($load->getPayment())->report();
        }

        return 'OK';
    }
}
