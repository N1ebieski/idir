<?php

namespace N1ebieski\IDir\Utils\Payment\Interfaces\Codes;

use GuzzleHttp\Psr7\Response as GuzzleResponse;

interface TransferUtilStrategy
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
     * @param string $code
     * @param string $id
     * @return GuzzleResponse
     */
    public function makeResponse(string $code, string $id) : GuzzleResponse;
}
