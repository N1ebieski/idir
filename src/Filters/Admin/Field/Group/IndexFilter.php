<?php

namespace N1ebieski\IDir\Filters\Admin\Field\Group;

use N1ebieski\IDir\Filters\Filter;
use N1ebieski\IDir\Models\Group;

/**
 * [IndexFilter description]
 */
class IndexFilter extends Filter
{
    /**
     * [protected description]
     * @var array
     */
    protected $filters = ['search', 'visible', 'type', 'morph', 'orderby', 'paginate'];

    /**
     * [setMorph description]
     * @param Group $group [description]
     */
    public function setMorph(Group $group)
    {
        $this->parameters['morph'] = $group;

        return $this;
    }

    /**
     * [filterMorph description]
     * @param int|null $id [description]
     */
    public function filterMorph(int $id = null) : void
    {
        $this->parameters['morph'] = null;

        if ($id !== null) {
            if ($group = $this->findMorph($id))
            {
                $this->setMorph($group);
            }
        }
    }

    /**
     * [findMorph description]
     * @param  int   $id [description]
     * @return Group     [description]
     */
    protected function findMorph(int $id) : Group
    {
        return Group::find($id, ['id', 'name']);
    }
}
