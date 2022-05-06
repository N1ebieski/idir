<?php

namespace N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\Responses;

interface AuthorizeResponseInterface
{
    /**
     *
     * @return bool
     */
    public function isSuccessful(): bool;
}
