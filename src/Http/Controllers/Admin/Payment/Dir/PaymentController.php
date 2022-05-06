<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Payment\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Loads\Admin\Payment\ShowLoad;
use N1ebieski\IDir\Http\Controllers\Admin\Payment\Dir\Polymorphic;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\TransferClientInterface;

class PaymentController implements Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Payment $payment
     * @param string $driver
     * @param ShowLoad $load
     * @param TransferClientInterface $client
     * @return RedirectResponse
     */
    public function show(
        Payment $payment,
        string $driver = null,
        ShowLoad $load,
        TransferClientInterface $client
    ): RedirectResponse {
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
                'redirect' => URL::route('admin.dir.index'),
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
