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

namespace N1ebieski\IDir\Http\Clients\Payment\Cashbill\Transfer\Responses;

use Psr\Http\Message\ResponseInterface;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\Responses\PurchaseResponseInterface;

class PurchaseResponse implements PurchaseResponseInterface
{
    /**
     * Constructor.
     * @param ResponseInterface $response
     */
    public function __construct(protected ResponseInterface $response)
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
        $redirects = $this->response->getHeader(\GuzzleHttp\RedirectMiddleware::HISTORY_HEADER);

        return end($redirects) ?: '';
    }
}
