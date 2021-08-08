<?php

namespace N1ebieski\IDir\Repositories;

use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\Repositories\PermissionRepo as BasePermissionRepo;

class PermissionRepo extends BasePermissionRepo
{
    /**
     * [getUserWithRole description]
     * @param  int        $id [description]
     * @return Collection     [description]
     */
    public function getUserWithRole(int $id): Collection
    {
        return $this->permission->with([
                'roles' => function ($query) use ($id) {
                    $query->where('id', $id);
                }
            ])
            ->where('name', 'like', 'web.%')
            ->orWhere('name', 'like', 'api.%')
            ->orderBy('name', 'asc')
            ->get();
    }
}
