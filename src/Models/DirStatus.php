<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Repositories\DirStatus\DirStatusRepo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\IDir\Services\DirStatus\DirStatusService;

class DirStatus extends Model
{
    use HasFactory;

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

    /**
     * Create a new factory instance for the model.
     *
     * @return DirStatusFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\IDir\Database\Factories\DirStatus\DirStatusFactory::new();
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

    // Factories

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

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return DirStatusFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
