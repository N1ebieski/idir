<?php

namespace N1ebieski\IDir\Models\Map;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Models\Traits\HasPolymorphic;

class Map extends Model
{
    use HasPolymorphic;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lat',
        'long'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'model_id' => 'integer',
        'lat' => 'decimal:14',
        'long' => 'decimal:14',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
