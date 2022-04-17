<?php

namespace N1ebieski\IDir\Http\Responses\Data\Gus;

use GusApi\SearchReport as GusReport;

interface DataInterface
{
    /**
     * Undocumented function
     *
     * @param GusReport $gusReport
     * @return array
     */
    public function toArray(GusReport $gusReport): array;
}
