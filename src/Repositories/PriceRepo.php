<?php

namespace N1ebieski\IDir\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use N1ebieski\IDir\Models\Price;
use N1ebieski\IDir\Models\Code;

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
     * [paginate description]
     * @param  array        $filter [description]
     * @return LengthAwarePaginator [description]
     */
    public function paginateByFilter(array $filter) : LengthAwarePaginator
    {
        return $this->price->with('group')
            ->filterSearch($filter['search'])
            ->filterExcept($filter['except'])
            ->filterGroup($filter['group'])
            ->filterType($filter['type'])
            ->when($filter['orderby'] === null, function ($query) use ($filter) {
                $query->filterOrderBySearch($filter['search']);
            })
            ->filterOrderBy($filter['orderby'] ?? 'price|asc')
            ->filterPaginate($filter['paginate']);
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
