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

namespace N1ebieski\IDir\Filters\Traits;

use N1ebieski\IDir\Models\Region\Region;

trait HasRegion
{
    /**
     *
     * @param Region $region
     * @return self
     */
    public function setRegion(Region $region): self
    {
        $this->parameters['region'] = $region;

        return $this;
    }

    /**
     *
     * @param int|null $id
     * @return void
     */
    public function filterRegion(int $id = null)
    {
        $this->parameters['region'] = null;

        if ($id !== null) {
            if ($region = $this->findRegion($id)) {
                $this->setRegion($region);
            }
        }
    }

    /**
     *
     * @param int|null $id
     * @return null|Region
     */
    public function findRegion(int $id = null): ?Region
    {
        return Region::find($id);
    }
}
