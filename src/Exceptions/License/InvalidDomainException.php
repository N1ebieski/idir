<?php

namespace N1ebieski\IDir\Exceptions\License;

use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Exceptions\CustomException;

class InvalidDomainException extends CustomException
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    public $message = 'The license is for another domain';

    /**
     * Undocumented variable
     *
     * @var int
     */
    public $code = HttpResponse::HTTP_FORBIDDEN;
}
