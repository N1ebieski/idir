<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Http\Responses\Data\Gus\Field\Value\Types;

use Illuminate\Support\Str;
use GusApi\SearchReport as GusReport;
use N1ebieski\IDir\Models\Region\Region;
use N1ebieski\IDir\Http\Responses\Data\Gus\Field\Value\Types\Value;

class Regions extends Value
{
    /**
     * Undocumented function
     *
     * @param GusReport $gusReport
     * @param Region $region
     * @param Str $str
     */
    public function __construct(
        GusReport $gusReport,
        protected Region $region,
        protected Str $str
    ) {
        parent::__construct($gusReport);
    }

    /**
     * Undocumented function
     *
     * @return int|null
     */
    public function handle(): ?int
    {
        $province = $this->str->slug($this->gusReport->getProvince());

        if (!empty($province)) {
            $region = $this->region->makeCache()->rememberBySlug($province);

            return $region?->id;
        }

        return null;
    }
}
