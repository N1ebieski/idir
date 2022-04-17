<?php

namespace N1ebieski\IDir\Http\Responses\Data\Gus\Field\Value\Types;

use N1ebieski\IDir\Http\Responses\Data\Gus\Field\Value\Types\Value;

class Map extends Value
{
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
