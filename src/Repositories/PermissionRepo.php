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
                'web.*',
                'web.comments.*',
                'web.comments.create',
                'web.comments.suggest',
                'web.comments.edit',
                'web.dirs.*',
                'web.dirs.create',
                'web.dirs.edit',
                'web.dirs.delete',
                'web.dirs.notification'
            ])
            ->orderBy('name', 'asc')
            ->get();
    }
}
