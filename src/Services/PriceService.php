<?php

namespace N1ebieski\IDir\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as Collect;
use N1ebieski\IDir\Models\Price;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use N1ebieski\ICore\Services\Interfaces\Updatable;

/**
 * [PriceService description]
 */
class PriceService implements Creatable, Updatable
{
    /**
     * Model
     * @var Price
     */
    protected $price;

    /**
     * [protected description]
     * @var Collection
     */
    protected $prices;

    /**
     * [private description]
     * @var Collect
     */
    protected $collect;

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
     * @param Price $price
     *
     * @return static
     */
    public function setPrice(Price $price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * [organizeGlobal description]
     * @param array $attributes [description]
     */
    public function organizeGlobal(array $attributes) : void
    {
        $this->deleteExceptGlobal(
            $this->collect->make($attributes)->pluck('id')->toArray()
        );

        $this->createOrUpdateGlobal($attributes);
    }

    /**
     * [findByIds description]
     * @param  array      $ids [description]
     * @return Collection      [description]
     */
    public function findByIds(array $ids) : Collection
    {
        return $this->prices = $this->price->makeRepo()
            ->getByIds($ids)
            ->map(function($item) {
                return $item->setGroup($this->price->getGroup());
            });
    }

    /**
     * [createOrUpdateGlobal description]
     * @param array $attributes [description]
     */
    public function createOrUpdateGlobal(array $attributes) : void
    {
        $this->findByIds(
            $this->collect->make($attributes)->pluck('id')->toArray()
        );

        foreach ($attributes as $attribute) {
            if (isset($attribute['id'])
            && ($price = $this->prices->where('id', $attribute['id'])->first()) instanceof Price) {
                $this->setPrice($price)->update($attribute);
            } else {
                $this->create($attribute);
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

        $this->price->codes()->make()
            ->setPrice($price)
            ->makeService()
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
        $this->price->codes()->make()
            ->setPrice($this->price)
            ->makeService()
            ->organizeGlobal($attributes['codes'] ?? []);

        return $this->price->update($attributes);
    }

    /**
     * [deleteNotExists description]
     * @param  array $ids [description]
     * @return int          [description]
     */
    public function deleteExceptGlobal(array $ids) : int
    {
        return $this->price->whereNotIn('id', array_filter($ids))
            ->where('group_id', $this->price->getGroup()->id)->delete();
    }
}
