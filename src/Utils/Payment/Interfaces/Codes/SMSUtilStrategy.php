<?php

namespace N1ebieski\IDir\Utils\Payment\Interfaces\Codes;

interface SMSUtilStrategy
{
    /**
     * Get [protected description]
     *
     * @return  object
     */
    public function getContents();

    /**
     * [authorize description]
     * @param array $attributes [description]
     */
    public function authorize(array $attributes): void;
}
