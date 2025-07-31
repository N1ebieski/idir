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

namespace N1ebieski\IDir\Http\Controllers\Web\Payment\Dir;

use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Exceptions\Payment\Exception;
use N1ebieski\IDir\Loads\Web\Payment\Dir\ShowLoad;
use N1ebieski\IDir\Loads\Web\Payment\Dir\CompleteLoad;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use N1ebieski\IDir\Http\Controllers\Web\Payment\Dir\Polymorphic;
use N1ebieski\IDir\Http\Requests\Web\Payment\Interfaces\CompleteRequestInterface;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\TransferClientInterface;

class PaymentController extends Controller implements Polymorphic
{
    /**
     *
     * @param Payment $payment
     * @param ShowLoad $load
     * @param TransferClientInterface $client
     * @param string|null $driver
     * @return RedirectResponse
     * @throws Exception
     * @throws RouteNotFoundException
     */
    public function show(
        Payment $payment,
        ShowLoad $load,
        TransferClientInterface $client,
        ?string $driver = null
    ): RedirectResponse {
        try {
            $response = $client->purchase([
                'amount' => $payment->order->price,
                'desc' => Lang::get('idir::payments.dir.desc', [
                    'title' => $payment->morph->title,
                    'group' => $payment->order->group->name,
                    'days' => $days = $payment->order->days,
                    'limit' => $days !== null ?
                        mb_strtolower(Lang::get('idir::prices.days'))
                        : strtolower(Lang::get('idir::prices.unlimited'))
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

        return Response::redirectTo($response->getUrlToPayment());
    }

    /**
     *
     * @param CompleteRequestInterface $request
     * @param CompleteLoad $load
     * @param TransferClientInterface $client
     * @param string|null $driver
     * @return RedirectResponse
     * @throws Exception
     * @throws RouteNotFoundException
     */
    public function complete(
        CompleteRequestInterface $request,
        CompleteLoad $load,
        TransferClientInterface $client,
        string $driver = null
    ): RedirectResponse {
        try {
            $response = $client->complete([
                'amount' => $load->getPayment()->order->price,
                'notifyUrl' => URL::route('api.payment.dir.verify', [$driver])
            ], $request->validated());
        } catch (\N1ebieski\IDir\Exceptions\Payment\Exception $e) {
            throw $e->setPayment($load->getPayment());
        }

        return Response::redirectTo($request->input('redirect'))->with(
            $response->isSuccessful() ? 'success' : 'danger',
            $response->isSuccessful() ?
                Lang::get('idir::payments.success.complete')
                : Lang::get('idir::payments.error.complete')
        );
    }
}
