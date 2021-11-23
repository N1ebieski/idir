<?php

namespace N1ebieski\IDir\Http\Responses\Data\Field\Value\Types;

use GusApi\SearchReport as GusReport;
use N1ebieski\IDir\Http\Responses\Data\Field\Value\Types\Value;

class Map extends Value
{
    /**
     * Undocumented function
     *
     * @param GusReport $gusReport
     */
    public function __construct(GusReport $gusReport)
    {
        parent::__construct($gusReport);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function __invoke(): string
    {
        return $this->gusReport->getStreet()
            . ' ' . $this->gusReport->getPropertyNumber()
            . '/' . $this->gusReport->getApartmentNumber()
            . ', ' . $this->gusReport->getZipCode()
            . ' ' . $this->gusReport->getCity();
    }
}
