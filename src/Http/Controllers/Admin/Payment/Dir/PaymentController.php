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

namespace N1ebieski\IDir\Http\Controllers\Admin\Payment\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Exceptions\Payment\Exception;
use N1ebieski\IDir\Loads\Admin\Payment\Dir\ShowLoad;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use N1ebieski\IDir\Http\Controllers\Admin\Payment\Dir\Polymorphic;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\TransferClientInterface;

class PaymentController implements Polymorphic
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
                        : mb_strtolower(Lang::get('idir::prices.unlimited'))
                ]),
                'uuid' => $payment->uuid,
                'redirect' => URL::route('admin.dir.index', [
                    'filter' => [
                        'search' => "id:\"{$payment->morph->id}\""
                    ]
                ]),
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
     * [showLogs description]
     *
     * @param   Dir           $dir  [$dir description]
     * @return  JsonResponse        [return description]
     */
    public function showLogs(Dir $dir): JsonResponse
    {
        return Response::json([
            'view' => View::make('idir::admin.payment.show_logs', [
                'payments' => $dir->makeRepo()->getPayments(),
            ])->render()
        ]);
    }
}
