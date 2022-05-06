<?php

namespace N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer;

use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\Responses\CompleteResponseInterface;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\Responses\PurchaseResponseInterface;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\Responses\AuthorizeResponseInterface;

/**
 *
 * @author Mariusz Wysokiński <kontakt@intelekt.net.pl>
 */
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
