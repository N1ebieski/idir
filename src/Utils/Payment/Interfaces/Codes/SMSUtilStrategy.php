<?php

namespace N1ebieski\IDir\Utils\Payment\Interfaces\Codes;

use GuzzleHttp\Psr7\Response as GuzzleResponse;

interface SMSUtilStrategy
{
    /**
     * Get [protected description]
     *
     * @return  object
     */
    public function getResponse();

    /**
     * [authorize description]
     * @param array $attributes [description]
     */
    public function authorize(array $attributes) : void;
    
    /**
     * Undocumented function
     *
     * @param string $token
     * @param string $code
     * @return GuzzleResponse
     */
    public function makeResponse(string $token, string $code) : GuzzleResponse;
}
