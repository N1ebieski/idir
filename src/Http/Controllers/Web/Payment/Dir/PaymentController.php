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
use N1ebieski\IDir\Http\Requests\Web\Payment\Interfaces\CompleteRequestInterface;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\TransferClientInterface;

class PaymentController extends Controller implements Polymorphic
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

        return Response::redirectTo($response->getUrlToPayment());
    }

    /**
     * Undocumented function
     *
     * @param string $driver
     * @param CompleteRequestInterface $request
     * @param CompleteLoad $load
     * @param TransferClientInterface $client
     * @return RedirectResponse
     */
    public function complete(
        string $driver = null,
        CompleteRequestInterface $request,
        CompleteLoad $load,
        TransferClientInterface $client
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
