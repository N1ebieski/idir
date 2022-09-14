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

namespace N1ebieski\IDir\Repositories\Region;

use InvalidArgumentException;
use N1ebieski\IDir\Models\Region\Region;

class RegionRepo
{
    /**
     * [__construct description]
     * @param Region $region [description]
     */
    public function __construct(protected Region $region)
    {
        //
    }

    /**
     *
     * @param string $slug
     * @return null|Region
     * @throws InvalidArgumentException
     */
    public function firstBySlug(string $slug): ?Region
    {
        return $this->region->newQuery()->where('slug', $slug)->first();
    }
}
