<?php

namespace N1ebieski\IDir\Repositories;

use N1ebieski\IDir\Models\Field\Field;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * [FieldRepo description]
 */
class FieldRepo
{
    /**
     * [private description]
     * @var Field
     */
    protected $field;

    /**
     * [__construct description]
     * @param Field    $field    [description]
     */
    public function __construct(Field $field)
    {
        $this->field = $field;
    }

    /**
     * [paginateByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter) : LengthAwarePaginator
    {
        return $this->field->poliType()
            ->filterSearch($filter['search'])
            ->filterVisible($filter['visible'])
            ->filterType($filter['type'])
            ->filterMorph($filter['morph'])
            ->filterOrderBy($filter['orderby'] ?? 'position|asc')
            ->filterPaginate($filter['paginate']);
    }

    /**
     * [getSiblingsAsArray description]
     * @return array [description]
     */
    public function getSiblingsAsArray() : array
    {
        return $this->field->siblings()
            ->get(['id', 'position'])
            ->pluck('position', 'id')
            ->toArray();
    }
}
