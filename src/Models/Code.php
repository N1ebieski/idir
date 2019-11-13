<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Services\CodeService;

/**
 * [Code description]
 */
class Code extends Model
{
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

    // Relations

    /**
     * [price description]
     * @return [type] [description]
     */
    public function price()
    {
        return $this->belongsTo('N1ebieski\IDir\Models\Price');
    }

    // Makers

    /**
     * [makeService description]
     * @return CodeService [description]
     */
    public function makeService() : CodeService
    {
        return app()->make(CodeService::class, ['code' => $this]);
    }
}
