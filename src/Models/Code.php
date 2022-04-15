<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Services\CodeService;
use N1ebieski\IDir\Repositories\CodeRepo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\IDir\Database\Factories\Code\CodeFactory;

class Code extends Model
{
    use HasFactory;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'quantity'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'price_id' => 'integer',
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return CodeFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\IDir\Database\Factories\Code\CodeFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return BelongsTo
     */
    public function price(): BelongsTo
    {
        return $this->belongsTo(\N1ebieski\IDir\Models\Price::class);
    }

    // Factories

    /**
     * [makeService description]
     * @return CodeService [description]
     */
    public function makeService()
    {
        return App::make(CodeService::class, ['code' => $this]);
    }

    /**
     * [makeRepo description]
     * @return CodeRepo [description]
     */
    public function makeRepo()
    {
        return App::make(CodeRepo::class, ['code' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return CodeFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
