<?php

namespace N1ebieski\IDir\Models\BanModel\Dir;

use N1ebieski\ICore\Models\BanModel\BanModel as BaseBanModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Polymorphic BanModel for dirs
 */
class BanModel extends BaseBanModel
{
    /**
     * The columns of the full text index
     *
     * @var array
     */
    protected $searchable = ['users.name', 'users.email', 'users.ip'];

    /**
     * [getModelTypeAttribute description]
     * @return [type] [description]
     */
    public function getModelTypeAttribute()
    {
        return 'N1ebieski\\ICore\\Models\\User';
    }

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return 'N1ebieski\\ICore\\Models\\BanModel\\BanModel';
    }

    // Accessors

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute() : string
    {
        return 'user';
    }

    // Repositories

    /**
     * [paginateByFilter description]
     * @param  array  $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter) : LengthAwarePaginator
    {
        return $this->select('users.id as id_user', 'users.*', 'bans_models.*', 'bans_models.id as id_ban')
            ->leftJoin('users', function ($query) {
                $query->on('bans_models.model_id', '=', 'users.id');
                $query->where('bans_models.model_type', '=', 'N1ebieski\ICore\Models\User');
            })
            ->filterExcept($filter['except'])
            ->filterSearch($filter['search'])
            ->filterOrderBy($filter['orderby'])
            ->filterPaginate($filter['paginate']);
    }
}
