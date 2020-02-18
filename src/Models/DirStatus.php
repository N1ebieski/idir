<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Repositories\DirStatusRepo;
use N1ebieski\IDir\Services\DirBacklinkService;

/**
 * [DirStatus description]
 */
class DirStatus extends Model
{
    // Configuration

    // /**
    //  * [private description]
    //  * @var Dir
    //  */
    // protected $dir;

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

    // // Setters

    // /**
    //  * @param Dir $dir
    //  *
    //  * @return static
    //  */
    // public function setDir(Dir $dir)
    // {
    //     $this->dir = $dir;

    //     return $this;
    // }

    // // Getters

    // public function getDir() : Dir
    // {
    //     return $this->dir;
    // }

    // Relations

    /**
     * [dir description]
     * @return [type] [description]
     */
    public function dir()
    {
        return $this->belongsTo('N1ebieski\IDir\Models\Dir');
    }

    // // Makers

    /**
     * [makeRepo description]
     * @return DirStatusRepo [description]
     */
    public function makeRepo()
    {
        return app()->make(DirStatusRepo::class, ['dirStatus' => $this]);
    }

    // /**
    //  * [makeService description]
    //  * @return DirBacklinkService [description]
    //  */
    // public function makeService()
    // {
    //     return app()->make(DirBacklinkService::class, ['dirBacklink' => $this]);
    // }

}
