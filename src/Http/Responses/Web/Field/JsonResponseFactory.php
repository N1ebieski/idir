<?php

namespace N1ebieski\IDir\Http\Responses\Web\Field;

use Illuminate\Http\JsonResponse;
use GusApi\SearchReport as GusReport;

interface JsonResponseFactory
{
    /**
     * Undocumented function
     *
     * @param GusReport|null $gusReport
     * @return JsonResponse
     */
    public function makeResponse(GusReport $gusReport = null): JsonResponse;
}
