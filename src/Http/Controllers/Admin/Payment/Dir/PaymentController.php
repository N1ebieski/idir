<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Payment\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Loads\Admin\Payment\ShowLoad;
use N1ebieski\IDir\Utils\Payment\Interfaces\TransferUtilStrategy;
use N1ebieski\IDir\Http\Controllers\Admin\Payment\Dir\Polymorphic;

/**
 * [PaymentController description]
 */
class PaymentController implements Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Payment $payment
     * @param ShowLoad $load
     * @param TransferUtilStrategy $transferUtil
     * @return RedirectResponse
     */
    public function show(
        Payment $payment,
        ShowLoad $load,
        TransferUtilStrategy $transferUtil
    ) : RedirectResponse {
        try {
            $transferUtil->setup([
                'amount' => $payment->order->price,
                'desc' => trans('idir::payments.dir.desc', [
                    'title' => $payment->morph->title,
                    'group' => $payment->order->group->name,
                    'days' => $days = $payment->order->days,
                    'limit' => $days !== null ?
                        strtolower(Lang::get('idir::groups.days'))
                        : strtolower(Lang::get('idir::groups.unlimited'))
                ]),
                'userdata' => json_encode([
                    'uuid' => $payment->uuid,
                    'redirect' => URL::route('admin.dir.index')
                ])
            ])
            ->makeResponse();
        } catch (\N1ebieski\IDir\Exceptions\Payment\Exception $e) {
            throw $e->setPayment($payment);
        }

        return Response::redirectTo($transferUtil->getUrlToPayment());
    }

    /**
     * [showLogs description]
     *
     * @param   Dir           $dir  [$dir description]
     *
     * @return  JsonResponse        [return description]
     */
    public function showLogs(Dir $dir) : JsonResponse
    {
        return Response::json([
            'success' => '',
            'view' => View::make('idir::admin.payment.show_logs', [
                'payments' => $dir->makeRepo()->getPayments(),
            ])->render()
        ]);
    }
}
