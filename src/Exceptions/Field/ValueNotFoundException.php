<?php

namespace N1ebieski\IDir\Exceptions\Field;

use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Exceptions\CustomException;

class ValueNotFoundException extends CustomException
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    public $message = 'Field value not found';

    /**
     * Undocumented variable
     *
     * @var int
     */
    public $code = HttpResponse::HTTP_FORBIDDEN;
}
