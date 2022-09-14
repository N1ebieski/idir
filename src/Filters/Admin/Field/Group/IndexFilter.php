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

namespace N1ebieski\IDir\Filters\Admin\Field\Group;

use N1ebieski\IDir\Models\Group;
use N1ebieski\ICore\Filters\Filter;
use N1ebieski\ICore\Filters\Traits\HasType;
use N1ebieski\ICore\Filters\Traits\HasMorph;
use N1ebieski\ICore\Filters\Traits\HasExcept;
use N1ebieski\ICore\Filters\Traits\HasSearch;
use N1ebieski\IDir\Filters\Traits\HasVisible;
use N1ebieski\ICore\Filters\Traits\HasOrderBy;
use N1ebieski\ICore\Filters\Traits\HasPaginate;

class IndexFilter extends Filter
{
    use HasExcept;
    use HasSearch;
    use HasVisible;
    use HasType;
    use HasMorph;
    use HasOrderBy;
    use HasPaginate;

    /**
     *
     * @param Group $group
     * @return self
     */
    public function setMorph(Group $group): self
    {
        $this->parameters['morph'] = $group;

        return $this;
    }

    /**
     * [filterMorph description]
     * @param int|null $id [description]
     */
    public function filterMorph(int $id = null): void
    {
        $this->parameters['morph'] = null;

        if ($id !== null) {
            if ($group = $this->findMorph($id)) {
                $this->setMorph($group);
            }
        }
    }

    /**
     *
     * @param int $id
     * @return null|Group
     */
    protected function findMorph(int $id): ?Group
    {
        return Group::find($id, ['id', 'name']);
    }
}
