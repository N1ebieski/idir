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

use N1ebieski\IDir\Models\Group;

trait HasGroup
{
    /**
     *
     * @param Group $group
     * @return self
     */
    public function setGroup(Group $group): self
    {
        $this->parameters['group'] = $group;

        return $this;
    }

    /**
     * [filterGroup description]
     * @param int|null $id [description]
     */
    public function filterGroup(int $id = null): void
    {
        $this->parameters['group'] = null;

        if ($id !== null) {
            if ($group = $this->findGroup($id)) {
                $this->setGroup($group);
            }
        }
    }

    /**
     *
     * @param int $id
     * @return null|Group
     */
    protected function findGroup(int $id): ?Group
    {
        return Group::find($id);
    }
}
