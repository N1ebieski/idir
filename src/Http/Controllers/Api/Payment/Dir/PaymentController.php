<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Http\Controllers\Api\Payment\Dir;

use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Event;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Exceptions\Payment\Exception;
use N1ebieski\IDir\Loads\Api\Payment\Dir\ShowLoad;
use N1ebieski\IDir\Loads\Api\Payment\Dir\VerifyLoad;
use N1ebieski\IDir\Events\Api\Payment\Dir\VerifyAttemptEvent;
use Illuminate\Contracts\Container\BindingResolutionException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
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
     * @apiResource N1ebieski\IDir\Http\Resources\Payment\Dir\PaymentResource
     * @apiResourceModel N1ebieski\IDir\Models\Payment\Dir\Payment states=pending,withMorph,withOrder with=morph,morph.group,morph.ratings,morph.user,orderMorph
     * @apiResourceAdditional url="https://paytest.cashbill.pl/pl/payment/eydJpZCI6IlRFU1RfNmV6OWZ6dXpvIiwicGMiOiIiLCJ0b2tlbiI6ImJiNjQ3ZGFhOTQ3NDU1NzM0OGRhMzhkYjEyMTE0YTI5MTA0NDhkMGUifQ--"
     *
     * @param Payment $payment
     * @param ShowLoad $load
     * @param TransferClientInterface $client
     * @param string|null $driver
     * @return JsonResponse
     * @throws Exception
     * @throws RouteNotFoundException
     * @throws BindingResolutionException
     */
    public function show(
        Payment $payment,
        ShowLoad $load,
        TransferClientInterface $client,
        ?string $driver = null
    ): JsonResponse {
        try {
            $response = $client->purchase([
                'amount' => $payment->order->price,
                'desc' => Lang::get('idir::payments.dir.desc', [
                    'title' => $payment->morph->title,
                    'group' => $payment->order->group->name,
                    'days' => $days = $payment->order->days,
                    'limit' => $days !== null ?
                        mb_strtolower(Lang::get('idir::prices.days'))
                        : mb_strtolower(Lang::get('idir::prices.unlimited'))
                ]),
                'uuid' => $payment->uuid,
                'redirect' => Auth::check() ?
                    URL::route('web.profile.dirs', [
                        'filter' => [
                            'search' => "id:\"{$payment->morph->id}\""
                        ]
                    ])
                    : URL::route('web.dir.create_1'),
                'notifyUrl' => URL::route('api.payment.dir.verify', [$driver]),
                'returnUrl' => URL::route('web.payment.dir.complete', [$driver]),
                'cancelUrl' => URL::route('web.payment.dir.complete', [$driver])
            ]);
        } catch (\N1ebieski\IDir\Exceptions\Payment\Exception $e) {
            throw $e->setPayment($payment);
        }

        return $payment->makeResource()
            ->additional(['url' => $response->getUrlToPayment()])
            ->response();
    }

    /**
     * @hideFromAPIDocumentation
     *
     * @param VerifyRequestInterface $request
     * @param VerifyLoad $load
     * @param TransferClientInterface $client
     * @param string|null $driver
     * @return string
     * @throws Throwable
     * @throws BindingResolutionException
     */
    public function verify(
        VerifyRequestInterface $request,
        VerifyLoad $load,
        TransferClientInterface $client,
        string $driver = null
    ): string {
        try {
            Event::dispatch(App::make(VerifyAttemptEvent::class, ['payment' => $load->getPayment()]));

            $client->authorize(['amount' => $load->getPayment()->order->price], $request->all());

            $load->getPayment()->makeService()->paid();

            Event::dispatch(App::make(VerifySuccessfulEvent::class, ['payment' => $load->getPayment()]));
        } catch (\N1ebieski\IDir\Exceptions\Payment\Exception $e) {
            $e->setPayment($load->getPayment())->report();
        }

        return 'OK';
    }
}
