<?php

namespace N1ebieski\IDir\Utils\Payment\Interfaces;

interface TransferUtilStrategy
{
    /**
     * Undocumented function
     *
     * @return void
     */
    public function purchase() : void;

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return void
     */
    public function complete(array $attributes) : void;

    /**
     * [authorize description]
     * @param  array  $attributes [description]
     * @return void               [description]
     */
    public function authorize(array $attributes) : void;
    
    /**
     * Undocumented function
     *
     * @return string
     */
    public function getUrlToPayment() : string;
}
