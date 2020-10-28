<?php

namespace N1ebieski\IDir\Http\Responses\Data\Field\Value\Types;

use GusApi\SearchReport as GusReport;

abstract class Value
{
    /**
     * Undocumented variable
     *
     * @var GusReport
     */
    protected $gusReport;

    /**
     * Undocumented function
     *
     * @param GusReport $gusReport
     */
    public function __construct(GusReport $gusReport)
    {
        $this->gusReport = $gusReport;
    }
}
