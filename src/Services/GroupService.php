<?php

namespace N1ebieski\IDir\Services;

use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
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
     * Model
     * @var Price
     */
    private $price;

    /**
     * [private description]
     * @var Collect
     */
    private $collect;

    /**
     * [__construct description]
     * @param Group     $group     [description]
     * @param Price     $price     [description]
     * @param Collect   $collect   [description]
     */
    public function __construct(Group $group, Price $price, Collect $collect)
    {
        $this->group = $group;
        $this->price = $price;
        $this->collect = $collect;
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model
    {
        $this->group->fill(
            $this->collect->make($attributes)->except(['priv', 'prices'])->toArray()
        );
        $this->group->save();

        $this->group->privileges()->attach(array_filter($attributes['priv'] ?? []));

        $this->price->getService()->setGroup($this->group)->createOrUpdateGlobal(array_filter($attributes['prices'] ?? []));

        return $this->group;
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool
    {
        $this->group->fill(
            $this->collect->make($attributes)->except(['priv', 'prices'])->toArray()
        );
        $result = $this->group->save();

        $this->group->privileges()->sync(array_filter($attributes['priv'] ?? []));

        $this->price->getService()->setGroup($this->group)->organizeGlobal(array_filter($attributes['prices'] ?? []));

        return $result;
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
        // W przypadku usuwania grupy trzeba zmienić alternative innych grup na Default 1
        $this->group->where('alt_id', $this->group->id)->update(['alt_id' => 1]);

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
