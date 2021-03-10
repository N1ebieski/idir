<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use N1ebieski\IDir\Repositories\DirStatusRepo;
use N1ebieski\IDir\Services\DirStatusService;

/**
 * [DirStatus description]
 */
class DirStatus extends Model
{
    // Configuration

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['attempts', 'attempted_at'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dirs_status';

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'attempts' => 0,
        'attempted_at' => null
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'dir_id' => 'integer',
        'attempts' => 'integer',
        'attempted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relations

    /**
     * [dir description]
     * @return [type] [description]
     */
    public function dir()
    {
        return $this->belongsTo('N1ebieski\IDir\Models\Dir');
    }

    // Makers

    /**
     * [makeRepo description]
     * @return DirStatusRepo [description]
     */
    public function makeRepo()
    {
        return App::make(DirStatusRepo::class, ['dirStatus' => $this]);
    }

    /**
     * [makeService description]
     * @return DirStatusService [description]
     */
    public function makeService()
    {
        return App::make(DirStatusService::class, ['dirStatus' => $this]);
    }
}
