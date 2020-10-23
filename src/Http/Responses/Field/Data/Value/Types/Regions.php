<?php

namespace N1ebieski\IDir\Http\Responses\Field\Data\Value\Types;

use GusApi\SearchReport as GusReport;
use N1ebieski\IDir\Http\Responses\Field\Data\Value\Types\Value;
use N1ebieski\IDir\Models\Region\Region;

class Regions extends Value
{
    /**
     * Undocumented variable
     *
     * @var Region
     */
    protected $region;

    /**
     * Undocumented function
     *
     * @param GusReport $gusReport
     */
    public function __construct(GusReport $gusReport, Region $region)
    {
        parent::__construct($gusReport);

        $this->region = $region;
    }

    /**
     * Undocumented function
     *
     * @return int|null
     */
    public function __invoke() : ?int
    {
        $province = strtolower($this->gusReport->getProvince());

        if ($province !== null) {
            $region = $this->region->makeCache()->rememberBySlug($province);

            return optional($region)->id;
        }

        return null;
    }
}
