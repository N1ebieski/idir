<?php

namespace N1ebieski\IDir\Exceptions\Payment\Cashbill\Transfer;

use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Exceptions\Payment\Cashbill\Exception;

class InvalidStatusException extends Exception
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    public $message = 'Invalid status of payment';

    /**
     * Undocumented variable
     *
     * @var int
     */
    public $code = HttpResponse::HTTP_FORBIDDEN;
}
