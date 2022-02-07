<?php

namespace N1ebieski\IDir\Exceptions\License;

use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Exceptions\CustomException;

class InvalidStatusException extends CustomException
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    public $message = 'License status is invalid';

    /**
     * Undocumented variable
     *
     * @var int
     */
    public $code = HttpResponse::HTTP_FORBIDDEN;
}
