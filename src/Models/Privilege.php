<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Repositories\PrivilegeRepo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relations

    /**
     * Undocumented function
     *
     * @return BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(\N1ebieski\IDir\Models\Group::class, 'groups_privileges');
    }

    // Factories

    /**
     * [makeRepo description]
     * @return PrivilegeRepo [description]
     */
    public function makeRepo()
    {
        return App::make(PrivilegeRepo::class, ['privilege' => $this]);
    }
}
