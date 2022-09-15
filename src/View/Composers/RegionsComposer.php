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

namespace N1ebieski\IDir\View\Composers;

use N1ebieski\IDir\Models\Region\Region;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\View\Composers\Composer;

class RegionsComposer extends Composer
{
    /**
     * Undocumented variable
     *
     * @var Collection
     */
    public $regions;

    /**
     * Undocumented function
     *
     * @param Region $region
     */
    public function __construct(Region $region)
    {
        $this->regions = $region->makeCache()->rememberAll();
    }
}
