<?php

namespace N1ebieski\IDir\Exceptions\Payment\Cashbill\Codes\SMS;

use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Exceptions\Payment\Cashbill\Exception;

class InvalidNumberException extends Exception
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    public $message = 'Number is invalid';

    /**
     * Undocumented variable
     *
     * @var int
     */
    public $code = HttpResponse::HTTP_FORBIDDEN;
}
