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

namespace N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer;

use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\Responses\CompleteResponseInterface;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\Responses\PurchaseResponseInterface;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\Responses\AuthorizeResponseInterface;

interface TransferClientInterface
{
    /**
     *
     * @param array $parameters
     * @return PurchaseResponseInterface
     */
    public function purchase(array $parameters): PurchaseResponseInterface;

    /**
     *
     * @param array $parameters
     * @param array $recievedParameters
     * @return CompleteResponseInterface
     */
    public function complete(array $parameters, array $recievedParameters): CompleteResponseInterface;

    /**
     *
     * @param array $parameters
     * @param array $recievedParameters
     * @return AuthorizeResponseInterface
     */
    public function authorize(array $parameters, array $recievedParameters): AuthorizeResponseInterface;
}
