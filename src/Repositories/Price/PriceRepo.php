<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Repositories\Price;

use InvalidArgumentException;
use N1ebieski\IDir\Models\Code;
use N1ebieski\IDir\Models\Price;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PriceRepo
{
    /**
     *
     * @param Price $price
     * @param Auth $auth
     * @return void
     */
    public function __construct(
        protected Price $price,
        protected Auth $auth
    ) {
        //
    }

    /**
     *
     * @param array $filter
     * @return LengthAwarePaginator
     * @throws InvalidArgumentException
     */
    public function paginateByFilter(array $filter): LengthAwarePaginator
    {
        // @phpstan-ignore-next-line
        return $this->price->newQuery()
            ->select('prices.*', 'groups.position')
            ->leftJoin('groups', 'prices.group_id', '=', 'groups.id')
            ->when(!is_null($filter['search']), function (Builder|Price $query) use ($filter) {
                return $query->filterSearch($filter['search'])
                    ->when($this->auth->user()?->can('admin.prices.view'), function (Builder $query) {
                        return $query->where(function (Builder $query) {
                            foreach (['id'] as $attr) {
                                $query = $query->when(array_key_exists($attr, $this->price->search), function (Builder $query) use ($attr) {
                                    return $query->where("{$this->price->getTable()}.{$attr}", $this->price->search[$attr]);
                                });
                            }

                            return $query;
                        });
                    });
            })
            ->filterExcept($filter['except'])
            ->filterGroup($filter['group'])
            ->filterType($filter['type'])
            ->filterOrderBy($filter['orderby'] ?? 'groups.position|asc')
            ->orderBy('prices.type', 'asc')
            ->orderBy('prices.price', 'asc')
            ->with('group')
            ->filterPaginate($filter['paginate']);
    }

    /**
     * [getByIds description]
     * @param  array      $ids [description]
     * @return Collection      [description]
     */
    public function getByIds(array $ids): Collection
    {
        return $this->price->newQuery()->whereIn('id', array_filter($ids))->get();
    }

    /**
     * [firstByCodeAndPriceId description]
     * @param  string $code [description]
     * @return Code|null       [description]
     */
    public function firstCodeByCode(string $code): ?Code
    {
        return $this->price->codes()->where('code', $code)->first();
    }
}
