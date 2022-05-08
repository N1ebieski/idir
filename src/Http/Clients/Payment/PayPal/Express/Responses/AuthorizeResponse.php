<?php

namespace N1ebieski\IDir\Http\Clients\Payment\PayPal\Express\Responses;

use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\Responses\AuthorizeResponseInterface;

/**
 *
 * @author Mariusz Wysokiński <kontakt@intelekt.net.pl>
 */
class AuthorizeResponse implements AuthorizeResponseInterface
{
    /**
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return true;
    }
}
