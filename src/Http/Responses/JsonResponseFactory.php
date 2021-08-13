<?php

namespace N1ebieski\IDir\Http\Responses;

use Illuminate\Http\JsonResponse;

interface JsonResponseFactory
{
    public function makeResponse(): JsonResponse;
}
