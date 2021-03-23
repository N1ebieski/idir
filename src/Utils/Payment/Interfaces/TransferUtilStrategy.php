<?php

namespace N1ebieski\IDir\Utils\Payment\Interfaces;

use GuzzleHttp\Psr7\Response as GuzzleResponse;

interface TransferUtilStrategy
{
    /**
     * [setup description]
     * @param  array $attributes [description]
     * @return static              [description]
     */
    public function setup(array $attributes);

    /**
     * [isSign description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function isSign(array $attributes) : bool;

    /**
     * [authorize description]
     * @param  array  $attributes [description]
     * @return void               [description]
     */
    public function authorize(array $attributes) : void;
    
    /**
     * Undocumented function
     */
    public function makeResponse();

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getUrlToPayment() : string;
}
