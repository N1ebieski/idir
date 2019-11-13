<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Repositories\PrivilegeRepo;

/**
 * [Privilege description]
 */
class Privilege extends Model
{
    // Relations

    public function groups()
    {
        return $this->belongsToMany('N1ebieski\IDir\Models\Group', 'groups_privileges');
    }

    // Makers

    /**
     * [makeRepo description]
     * @return PrivilegeRepo [description]
     */
    public function makeRepo() : PrivilegeRepo
    {
        return app()->make(PrivilegeRepo::class, ['privilege' => $this]);
    }
}
