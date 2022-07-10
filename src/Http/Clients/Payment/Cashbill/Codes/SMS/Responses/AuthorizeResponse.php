<?php

namespace N1ebieski\IDir\Http\Clients\Payment\Cashbill\Codes\SMS\Responses;

use N1ebieski\ICore\Http\Clients\Response;

class AuthorizeResponse extends Response
{
    /**
     * [isActive description]
     * @return bool [description]
     */
    public function isActive(): bool
    {
        return $this->get('active') === true;
    }

    /**
     * [isNumber description]
     * @param  int $number [description]
     * @return bool           [description]
     */
    public function isNumber(string $number): bool
    {
        return $this->get('number') === $number;
    }
}
