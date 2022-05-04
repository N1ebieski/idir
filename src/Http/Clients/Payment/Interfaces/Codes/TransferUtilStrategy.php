<?php

namespace N1ebieski\IDir\Utils\Payment\Interfaces\Codes;

interface TransferUtilStrategy
{
    /**
     * [authorize description]
     * @param array $attributes [description]
     */
    public function authorize(array $attributes): void;
}
