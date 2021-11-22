<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Services\DirBacklinkService;
use N1ebieski\IDir\Repositories\DirBacklinkRepo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DirBacklink extends Model
{
    // Configuration

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

    // Relations

    /**
     * Undocumented function
     *
     * @return BelongsTo
     */
    public function dir(): BelongsTo
    {
        return $this->belongsTo(\N1ebieski\IDir\Models\Dir::class);
    }

    /**
     * Undocumented function
     *
     * @return BelongsTo
     */
    public function link(): BelongsTo
    {
        return $this->belongsTo(\N1ebieski\ICore\Models\Link::class);
    }

    // Factories

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
