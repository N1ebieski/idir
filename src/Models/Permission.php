<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Models\Permission as BasePermission;
use N1ebieski\IDir\Repositories\PermissionRepo;

/**
 * [Permission description]
 */
class Permission extends BasePermission
{
    // Makers

    /**
     * [makeRepo description]
     * @return PermissionRepo [description]
     */
    public function makeRepo()
    {
        return App::make(PermissionRepo::class, ['permission' => $this]);
    }
}
