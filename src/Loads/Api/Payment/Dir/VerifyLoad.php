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

namespace N1ebieski\IDir\Loads\Api\Payment\Dir;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Models\Payment\Dir\Payment;

class VerifyLoad
{
    /**
     * Undocumented function
     *
     * @param Payment $payment
     * @param Request $request
     */
    public function __construct(protected Payment $payment, Request $request)
    {
        /** @var Payment|null */
        $dirPayment = $payment->makeRepo()->firstPendingByUuid($request->input('uuid'));

        if (is_null($dirPayment)) {
            App::abort(HttpResponse::HTTP_NOT_FOUND);
        }

        $this->payment = $dirPayment;
    }

    /**
     * Undocumented function
     *
     * @return Payment
     */
    public function getPayment(): Payment
    {
        return $this->payment;
    }
}
