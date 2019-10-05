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

    // Getters

    /**
     * [getRepo description]
     * @return PrivilegeRepo [description]
     */
    public function getRepo() : PrivilegeRepo
    {
        return app()->make(PrivilegeRepo::class, ['privilege' => $this]);
    }
}
