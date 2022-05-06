<?php

namespace N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\Responses;

interface CompleteResponseInterface
{
    /**
     *
     * @return bool
     */
    public function isSuccessful(): bool;
}
