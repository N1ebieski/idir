<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Repositories\DirBacklinkRepo;
use Carbon\Carbon;

/**
 * [DirBacklink description]
 */
class DirBacklink extends Model
{
    public $test = 'dupa';


    // Configuration

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['backlink', 'attempts', 'attempted_at'];

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
    public function makeRepo() : DirBacklinkRepo
    {
        return app()->make(DirBacklinkRepo::class, ['dirBacklink' => $this]);
    }

}
