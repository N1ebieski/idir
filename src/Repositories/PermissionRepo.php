<?php

namespace N1ebieski\IDir\Repositories;

use N1ebieski\ICore\Models\Permission;
use N1ebieski\ICore\Repositories\PermissionRepo as BasePermissionRepo;
use Illuminate\Database\Eloquent\Collection;

/**
 * [PermissionRepo description]
 */
class PermissionRepo extends BasePermissionRepo
{
    /**
     * [__construct description]
     * @param Permission $permission [description]
     */
    public function __construct(Permission $permission)
    {
        parent::__construct($permission);
    }

    /**
     * [getUserWithRole description]
     * @param  int        $id [description]
     * @return Collection     [description]
     */
    public function getUserWithRole(int $id) : Collection
    {
        return $this->permission->with([
                'roles' => function ($query) use ($id) {
                    $query->where('id', $id);
                }
            ])
            ->whereIn('name', [
                'create comments',
                'suggest comments',
                'edit comments',
                'create dirs',
                'edit dirs',
                'destroy dirs',
                'notification dirs'
            ])
            ->orderBy('name', 'asc')
            ->get();
    }
}
