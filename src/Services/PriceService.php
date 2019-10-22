<?php

namespace N1ebieski\IDir\Services;

use N1ebieski\ICore\Services\Serviceable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as Collect;
use N1ebieski\IDir\Models\Price;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Code;

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
     * @var Code
     */
    private $code;

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
     * [__construct description]
     * @param Price       $price   [description]
     * @param Code        $code    [description]
     * @param Collect   $collect   [description]
     */
    public function __construct(Price $price, Code $code, Collect $collect)
    {
        $this->price = $price;
        $this->code = $code;
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
     * [organize description]
     * @param array $prices [description]
     */
    public function organizeGlobal(array $prices) : void
    {
        $this->deleteExceptGlobal(
            $this->collect->make($prices)->pluck('id')->toArray()
        );

        $this->createOrUpdateGlobal($prices);
    }

    /**
     * [findById description]
     * @param  int    $id [description]
     * @return Price|null     [description]
     */
    public function findById(int $id) : ?Price
    {
        if ($price = $this->price->find($id)) {
            return $this->price = $price;
        }

        return null;
    }

    /**
     * [createOrUpdateGlobal description]
     * @param array $prices [description]
     */
    public function createOrUpdateGlobal(array $prices) : void
    {
        foreach ($prices as $price) {
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

        $this->code->getService()->setPrice($this->price)
            ->organizeGlobal($attributes['codes'] ?? []);

        return $this->price;
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool
    {
        $this->code->getService()->setPrice($this->price)
            ->organizeGlobal($attributes['codes'] ?? []);

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
     * [deleteNotExists description]
     * @param  array $ids [description]
     * @return int          [description]
     */
    public function deleteExceptGlobal(array $ids) : int
    {
        return $this->price->whereNotIn('id', $ids)
            ->where('group_id', $this->group->id)->delete();
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
