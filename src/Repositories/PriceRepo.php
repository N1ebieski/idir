<?php

namespace N1ebieski\IDir\Repositories;

use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Price;
use N1ebieski\IDir\Models\Code;

/**
 * [PriceRepo description]
 */
class PriceRepo
{
    /**
     * [private description]
     * @var Price
     */
    protected $price;

    /**
     * [__construct description]
     * @param Price $price [description]
     */
    public function __construct(Price $price)
    {
        $this->price = $price;
    }

    /**
     * [getByIds description]
     * @param  array      $ids [description]
     * @return Collection      [description]
     */
    public function getByIds(array $ids) : Collection
    {
        return $this->price->whereIn('id', array_filter($ids))->get();
    }

    /**
     * [firstByCodeAndPriceId description]
     * @param  string $code [description]
     * @return Code|null       [description]
     */
    public function firstCodeByCode(string $code) : ?Code
    {
        return $this->price->codes()->where('code', $code)->first();
    }
}
