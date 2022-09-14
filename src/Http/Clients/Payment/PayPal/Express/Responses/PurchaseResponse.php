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

namespace N1ebieski\IDir\Http\Clients\Payment\PayPal\Express\Responses;

use Omnipay\Common\Message\RedirectResponseInterface as OmniPayResponse;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\Responses\PurchaseResponseInterface;

class PurchaseResponse implements PurchaseResponseInterface
{
    /**
     * Constructor.
     * @param OmniPayResponse $response
     */
    public function __construct(protected OmniPayResponse $response)
    {
        //
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getUrlToPayment(): string
    {
        return $this->response->getRedirectUrl();
    }
}
