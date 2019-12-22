<?php

namespace N1ebieski\IDir\Filters;

use N1ebieski\ICore\Filters\Filter as BaseFilter;
use N1ebieski\IDir\Models\Group;

/**
 * [abstract description]
 */
abstract class Filter extends BaseFilter
{
    /**
     * [filterVisible description]
     * @param int|null $value [description]
     */
    public function filterVisible(int $value = null) : void
    {
        $this->parameters['visible'] = $value;
    }

    /**
     * [filterType description]
     * @param string|null $value [description]
     */
    public function filterType(string $value = null) : void
    {
        $this->parameters['type'] = $value;
    }

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
