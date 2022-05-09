<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\IDir\Services\DirBacklink\DirBacklinkService;
use N1ebieski\IDir\Repositories\DirBacklink\DirBacklinkRepo;
use N1ebieski\IDir\Database\Factories\DirBacklink\DirBacklinkFactory;

class DirBacklink extends Model
{
    use HasFactory;

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

    /**
     * Create a new factory instance for the model.
     *
     * @return DirBacklinkFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\IDir\Database\Factories\DirBacklink\DirBacklinkFactory::new();
    }

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

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return DirBacklinkFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
