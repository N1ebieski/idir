<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use N1ebieski\IDir\Repositories\PrivilegeRepo;

/**
 * [Privilege description]
 */
class Privilege extends Model
{
    // Configuration

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp'
    ];

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
    public function makeRepo()
    {
        return App::make(PrivilegeRepo::class, ['privilege' => $this]);
    }
}
