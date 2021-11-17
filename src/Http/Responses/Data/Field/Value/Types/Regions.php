<?php

namespace N1ebieski\IDir\Http\Responses\Data\Field\Value\Types;

use Illuminate\Support\Str;
use GusApi\SearchReport as GusReport;
use N1ebieski\IDir\Models\Region\Region;
use N1ebieski\IDir\Http\Responses\Data\Field\Value\Types\Value;

class Regions extends Value
{
    /**
     * Undocumented variable
     *
     * @var Region
     */
    protected $region;

    /**
     * Undocumented variable
     *
     * @var Str
     */
    protected $str;

    /**
     * Undocumented function
     *
     * @param GusReport $gusReport
     * @param Region $region
     * @param Str $str
     */
    public function __construct(GusReport $gusReport, Region $region, Str $str)
    {
        parent::__construct($gusReport);

        $this->region = $region;

        $this->str = $str;
    }

    /**
     * Undocumented function
     *
     * @return int|null
     */
    public function __invoke(): ?int
    {
        $province = $this->str->slug($this->gusReport->getProvince());

        if ($province !== null) {
            $region = $this->region->makeCache()->rememberBySlug($province);

            return optional($region)->id;
        }

        return null;
    }
}
