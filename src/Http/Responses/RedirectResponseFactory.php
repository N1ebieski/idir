<?php

namespace N1ebieski\IDir\Http\Responses;

use Illuminate\Http\RedirectResponse;

interface RedirectResponseFactory
{
    public function makeResponse() : RedirectResponse;
}
