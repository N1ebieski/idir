<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Repositories\DirBacklinkRepo;
use N1ebieski\IDir\Services\DirBacklinkService;

/**
 * [DirBacklink description]
 */
class DirBacklink extends Model
{
    // Configuration

    /**
     * [private description]
     * @var Dir
     */
    protected $dir;

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['url', 'attempts', 'attempted_at'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dirs_backlinks';

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
        'link_id' => 'integer',
        'attempts' => 'integer',
        'attempted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Setters

    /**
     * @param Dir $dir
     *
     * @return static
     */
    public function setDir(Dir $dir)
    {
        $this->dir = $dir;

        return $this;
    }

    // Getters

    public function getDir() : Dir
    {
        return $this->dir;
    }

    // Relations

    /**
     * [dir description]
     * @return [type] [description]
     */
    public function dir()
    {
        return $this->belongsTo('N1ebieski\IDir\Models\Dir');
    }

    /**
     * [link description]
     * @return [type] [description]
     */
    public function link()
    {
        return $this->belongsTo('N1ebieski\ICore\Models\Link');
    }

    // Makers

    /**
     * [makeRepo description]
     * @return DirBacklinkRepo [description]
     */
    public function makeRepo()
    {
        return App::make(DirBacklinkRepo::class, ['dirBacklink' => $this]);
    }

    /**
     * [makeService description]
     * @return DirBacklinkService [description]
     */
    public function makeService()
    {
        return App::make(DirBacklinkService::class, ['dirBacklink' => $this]);
    }
}
