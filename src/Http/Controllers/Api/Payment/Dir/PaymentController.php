<?php

namespace N1ebieski\IDir\Http\Controllers\Api\Payment\Dir;

use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Event;
use N1ebieski\IDir\Loads\Api\Payment\Dir\VerifyLoad;
use N1ebieski\IDir\Events\Api\Payment\Dir\VerifyAttemptEvent;
use N1ebieski\IDir\Events\Api\Payment\Dir\VerifySuccessfulEvent;
use N1ebieski\IDir\Http\Controllers\Api\Payment\Dir\Polymorphic;
use N1ebieski\IDir\Utils\Payment\Interfaces\TransferUtilStrategy;
use N1ebieski\IDir\Http\Requests\Api\Payment\Interfaces\VerifyRequestStrategy;

class PaymentController extends Controller implements Polymorphic
{
    /**
     * Undocumented function
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
    ) : string {
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
