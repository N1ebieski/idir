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

namespace N1ebieski\IDir\Repositories\Field;

use N1ebieski\IDir\Models\Field\Field;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FieldRepo
{
    /**
     * [__construct description]
     * @param Field    $field    [description]
     */
    public function __construct(protected Field $field)
    {
        //
    }

    /**
     * [paginateByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter): LengthAwarePaginator
    {
        return $this->field->newQuery()
            ->selectRaw("`{$this->field->getTable()}`.*")
            ->poliType()
            ->filterExcept($filter['except'])
            ->filterSearch($filter['search'])
            ->filterVisible($filter['visible'])
            ->filterType($filter['type'])
            ->filterMorph($filter['morph'])
            ->when(is_null($filter['orderby']), function (Builder|Field $query) use ($filter) {
                $query->filterOrderBySearch($filter['search']);
            })
            ->filterOrderBy($filter['orderby'] ?? 'position|asc')
            ->filterPaginate($filter['paginate']);
    }

    /**
     * [getSiblingsAsArray description]
     * @return array [description]
     */
    public function getSiblingsAsArray(): array
    {
        return $this->field->siblings()->pluck('position', 'id')->toArray();
    }
}
