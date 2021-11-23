<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Payment\Dir;

use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Loads\Web\Payment\Dir\ShowLoad;
use N1ebieski\IDir\Loads\Web\Payment\Dir\CompleteLoad;
use N1ebieski\IDir\Http\Controllers\Web\Payment\Dir\Polymorphic;
use N1ebieski\IDir\Utils\Payment\Interfaces\TransferUtilStrategy;
use N1ebieski\IDir\Http\Requests\Web\Payment\Interfaces\CompleteRequestStrategy;

class PaymentController extends Controller implements Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Payment $payment
     * @param string $driver
     * @param ShowLoad $load
     * @param TransferUtilStrategy $transferUtil
     * @return RedirectResponse
     */
    public function show(
        Payment $payment,
        string $driver = null,
        ShowLoad $load,
        TransferUtilStrategy $transferUtil
    ): RedirectResponse {
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
                ->setRedirect(
                    Auth::check() ?
                        URL::route('web.profile.edit_dir')
                        : URL::route('web.dir.create_1')
                )
                ->setNotifyUrl(URL::route('api.payment.dir.verify', [$driver]))
                ->setReturnUrl(URL::route('web.payment.dir.complete', [$driver]))
                ->setCancelUrl(URL::route('web.payment.dir.complete', [$driver]))
                ->purchase();
        } catch (\N1ebieski\IDir\Exceptions\Payment\Exception $e) {
            throw $e->setPayment($payment);
        }

        return Response::redirectTo($transferUtil->getUrlToPayment());
    }

    /**
     * Undocumented function
     *
     * @param string $driver
     * @param CompleteRequestStrategy $request
     * @param CompleteLoad $load
     * @param TransferUtilStrategy $transferUtil
     * @return RedirectResponse
     */
    public function complete(
        string $driver = null,
        CompleteRequestStrategy $request,
        CompleteLoad $load,
        TransferUtilStrategy $transferUtil
    ): RedirectResponse {
        try {
            $transferUtil->setAmount($load->getPayment()->order->price)
                ->setNotifyUrl(URL::route('api.payment.dir.verify', [$driver]))
                ->complete($request->validated());
        } catch (\N1ebieski\IDir\Exceptions\Payment\Exception $e) {
            throw $e->setPayment($load->getPayment());
        }

        return Response::redirectTo($request->input('redirect'))->with(
            $request->input('status') === 'ok' ? 'success' : 'danger',
            $request->input('status') === 'ok' ?
                Lang::get('idir::payments.success.complete')
                : Lang::get('idir::payments.error.complete')
        );
    }
}
