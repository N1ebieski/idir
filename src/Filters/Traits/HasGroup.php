<?php

namespace N1ebieski\IDir\Filters\Traits;

use N1ebieski\IDir\Models\Group;

/**
 * [trait description]
 */
trait HasGroup
{
    /**
     * [setGroup description]
     * @param Group $group [description]
     */
    public function setGroup(Group $group)
    {
        $this->parameters['group'] = $group;

        return $this;
    }

    /**
     * [filterGroup description]
     * @param int|null $id [description]
     */
    public function filterGroup(int $id = null) : void
    {
        $this->parameters['group'] = null;

        if ($id !== null) {
            if ($group = $this->findGroup($id))
            {
                $this->setGroup($group);
            }
        }
    }

    /**
     * [findGroup description]
     * @param  int   $id [description]
     * @return Group     [description]
     */
    protected function findGroup(int $id) : Group
    {
        return Group::find($id, ['id', 'name']);
    }
}
