<?php

namespace N1ebieski\IDir\Http\Clients\Payment\Cashbill\Codes\Transfer\Responses;

use N1ebieski\ICore\Http\Clients\Response;

class AuthorizeResponse extends Response
{
    /**
     * [isActive description]
     * @return bool [description]
     */
    public function isActive(): bool
    {
        return $this->get('status') === "OK";
    }
}
