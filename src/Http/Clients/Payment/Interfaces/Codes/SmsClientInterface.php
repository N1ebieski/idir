<?php

namespace N1ebieski\IDir\Http\Clients\Payment\Interfaces\Codes;

use N1ebieski\ICore\Http\Clients\Response;

interface SmsClientInterface
{
    /**
     * Undocumented function
     *
     * @param array $parameters
     * @return Response
     */
    public function authorize(array $parameters): Response;
}
