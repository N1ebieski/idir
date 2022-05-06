<?php

namespace N1ebieski\IDir\Http\Controllers\Api\Payment\Dir;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Event;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Loads\Api\Payment\Dir\ShowLoad;
use N1ebieski\IDir\Loads\Api\Payment\Dir\VerifyLoad;
use N1ebieski\IDir\Events\Api\Payment\Dir\VerifyAttemptEvent;
use N1ebieski\IDir\Http\Resources\Payment\Dir\PaymentResource;
use N1ebieski\IDir\Events\Api\Payment\Dir\VerifySuccessfulEvent;
use N1ebieski\IDir\Http\Controllers\Api\Payment\Dir\Polymorphic;
use N1ebieski\IDir\Http\Requests\Api\Payment\Interfaces\VerifyRequestInterface;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\TransferClientInterface;

/**
 * @group Payments
 *
 * > Routes:
 *
 *     /routes/vendor/idir/api/payments.php
 *
 * > Controller:
 *
 *     N1ebieski\IDir\Http\Controllers\Api\Payment\Dir\PaymentController
 *
 * > Resource:
 *
 *     N1ebieski\IDir\Http\Resources\Payment\PaymentResource
 */
class PaymentController extends Controller implements Polymorphic
{
    /**
     * Show dir's payment
     *
     * @urlParam payment_dir_pending string required The payment UUID. No-example
     * @urlParam driver string The payment driver. No-example
     *
     * @responseField uuid string
     * @responseField driver string
     * @responseField logs string (available only for admin.dirs.view).
     * @responseField status object
     * @responseField created_at string
     * @responseField updated_at string
     * @responseField morph object Contains relationship Dir.
     * @responseField order object Contains relationship Price.
     * @responseField url string Link to the driver's payment page (for transfer type payment).
     *
     * @apiResource N1ebieski\IDir\Http\Resources\Payment\Dir\PaymentResource
     * @apiResourceModel N1ebieski\IDir\Models\Payment\Dir\Payment states=pending,withMorph,withOrder with=morph,morph.group,morph.ratings,morph.user,orderMorph
     * @apiResourceAdditional url="https://paytest.cashbill.pl/pl/payment/eydJpZCI6IlRFU1RfNmV6OWZ6dXpvIiwicGMiOiIiLCJ0b2tlbiI6ImJiNjQ3ZGFhOTQ3NDU1NzM0OGRhMzhkYjEyMTE0YTI5MTA0NDhkMGUifQ--"
     *
     * @param Payment $payment
     * @param string $driver
     * @param ShowLoad $load
     * @param TransferClientInterface $client
     * @return JsonResponse
     */
    public function show(
        Payment $payment,
        string $driver = null,
        ShowLoad $load,
        TransferClientInterface $client
    ): JsonResponse {
        try {
            $response = $client->purchase([
                'amount' => $payment->order->price,
                'desc' => Lang::get('idir::payments.dir.desc', [
                    'title' => $payment->morph->title,
                    'group' => $payment->order->group->name,
                    'days' => $days = $payment->order->days,
                    'limit' => $days !== null ?
                        strtolower(Lang::get('idir::prices.days'))
                        : strtolower(Lang::get('idir::prices.unlimited'))
                ]),
                'uuid' => $payment->uuid,
                'redirect' => Auth::check() ?
                    URL::route('web.profile.dirs')
                    : URL::route('web.dir.create_1'),
                'notifyUrl' => URL::route('api.payment.dir.verify', [$driver]),
                'returnUrl' => URL::route('web.payment.dir.complete', [$driver]),
                'cancelUrl' => URL::route('web.payment.dir.complete', [$driver])
            ]);
        } catch (\N1ebieski\IDir\Exceptions\Payment\Exception $e) {
            throw $e->setPayment($payment);
        }

        return App::make(PaymentResource::class, ['payment' => $payment])
            ->additional(['url' => $response->getUrlToPayment()])
            ->response();
    }

    /**
     * @hideFromAPIDocumentation
     *
     * @param string $driver
     * @param VerifyRequestInterface $request
     * @param VerifyLoad $load
     * @param TransferClientInterface $client
     * @return string
     */
    public function verify(
        string $driver = null,
        VerifyRequestInterface $request,
        VerifyLoad $load,
        TransferClientInterface $client
    ): string {
        try {
            Event::dispatch(App::make(VerifyAttemptEvent::class, ['payment' => $load->getPayment()]));

            $client->authorize(['amount' => $load->getPayment()->order->price], $request->all());

            $load->getPayment()->makeRepo()->paid();

            Event::dispatch(App::make(VerifySuccessfulEvent::class, ['payment' => $load->getPayment()]));
        } catch (\N1ebieski\IDir\Exceptions\Payment\Exception $e) {
            $e->setPayment($load->getPayment())->report();
        }

        return 'OK';
    }
}
