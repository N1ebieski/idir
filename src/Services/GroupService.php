<?php

namespace N1ebieski\IDir\Services;

use N1ebieski\IDir\Models\Group\Group;
use N1ebieski\ICore\Services\Serviceable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as Collect;

/**
 * [GroupService description]
 */
class GroupService implements Serviceable
{
    /**
     * Model
     * @var Group
     */
    private $group;

    /**
     * [private description]
     * @var Collect
     */
    private $collect;

    /**
     * [__construct description]
     * @param Group     $group     [description]
     * @param Collect   $collect   [description]
     */
    public function __construct(Group $group, Collect $collect)
    {
        $this->group = $group;
        $this->collect = $collect;
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model
    {
        $group = $this->group->create(
            $this->collect->make($attributes)->except('priv')->toArray()
        );

        $group->privileges()->attach(array_filter($attributes['priv']) ?? []);

        return $group;
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool
    {
        $this->group->privileges()->sync(array_filter($attributes['priv']) ?? []);

        return $this->group->update(
            $this->collect->make($attributes)->except('priv')->toArray()
        );
    }

    /**
     * [updateStatus description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateStatus(array $attributes) : bool
    {

    }

    /**
     * [updatePosition description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updatePosition(array $attributes) : bool
    {
        return $this->group->update(['position' => (int)$attributes['position']]);
    }

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete() : bool
    {
        return $this->group->delete();
    }

    /**
     * [deleteGlobal description]
     * @param  array $ids [description]
     * @return int        [description]
     */
    public function deleteGlobal(array $ids) : int
    {

    }
}
