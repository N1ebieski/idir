<?php

namespace N1ebieski\IDir\Services;

use N1ebieski\ICore\Services\Serviceable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as Collect;
use N1ebieski\IDir\Models\Price;
use N1ebieski\IDir\Models\Group;

/**
 * [PriceService description]
 */
class PriceService implements Serviceable
{
    /**
     * Model
     * @var Price
     */
    private $price;

    /**
     * [private description]
     * @var Group
     */
    private $group;

    /**
     * [private description]
     * @var Collect
     */
    private $collect;

    /**
     * [protected description]
     * @var Collect
     */
    protected $prices;

    /**
     * [__construct description]
     * @param Price       $price   [description]
     * @param Collect   $collect   [description]
     */
    public function __construct(Price $price, Collect $collect)
    {
        $this->price = $price;
        $this->collect = $collect;
    }

    /**
     * @param Group $group
     *
     * @return static
     */
    public function setGroup(Group $group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * [setPrices description]
     * @param  array      $items [description]
     * @return Collect        [description]
     */
    protected function flattenPrices(array $items) : Collect
    {
        return $this->prices = $this->collect->make($items)->flatten(1);
    }

    /**
     * [organize description]
     * @param array $items [description]
     */
    public function organizeGlobal(array $items) : void
    {
        $this->deleteNotExistsGlobal($items);

        $this->createOrUpdateGlobal($items);
    }

    /**
     * [findById description]
     * @param  int    $id [description]
     * @return Price|null     [description]
     */
    public function findById(int $id) : ?Price
    {
        $price = $this->price->find($id);

        if ($price) {
            return $this->price = $price;
        }

        return null;
    }

    /**
     * [createOrUpdateGlobal description]
     * @param array $items [description]
     */
    public function createOrUpdateGlobal(array $items) : void
    {
        $this->flattenPrices($items);

        foreach ($this->prices as $price) {
            if (isset($price['id']) && $this->findById((int)$price['id'])) {
                $this->update($price);
            } else {
                $this->create($price);
            }
        }
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model
    {
        $this->price = $this->price->make($attributes);
        $this->price->group()->associate($this->group);
        $this->price->save();

        return $this->price;
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool
    {
        return $this->price->update($attributes);
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
     * [delete description]
     * @return bool [description]
     */
    public function delete() : bool
    {

    }

    /**
     * [deleteNotExistsGlobal description]
     * @param  array $items [description]
     * @return int          [description]
     */
    public function deleteNotExistsGlobal(array $items) : int
    {
        $this->flattenPrices($items);

        return $this->price->whereNotIn('id',
            $this->prices->pluck('id')->toArray()
        )->where('group_id', $this->group->id)->delete();
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
