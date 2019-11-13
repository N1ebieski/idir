<?php

namespace N1ebieski\IDir\Services;

use N1ebieski\ICore\Services\Serviceable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as Collect;
use N1ebieski\IDir\Models\Price;
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
    protected $price;

    /**
     * [private description]
     * @var Code
     */
    protected $code;

    /**
     * [private description]
     * @var Collect
     */
    protected $collect;

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
        $price = $this->price->make($attributes);

        $price->group()->associate($this->price->getGroup());
        $price->save();

        $this->code->setPrice($price)->makeService()
            ->organizeGlobal($attributes['codes'] ?? []);

        return $price;
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool
    {
        $this->code->setPrice($this->price)->makeService()
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
            ->where('group_id', $this->price->getGroup()->id)->delete();
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
