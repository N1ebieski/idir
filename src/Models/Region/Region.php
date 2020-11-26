<?php

namespace N1ebieski\IDir\Models\Region;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Models\Traits\Polymorphic;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\App;
use N1ebieski\IDir\Cache\RegionCache;
use N1ebieski\IDir\Repositories\RegionRepo;

/**
 * [Region description]
 */
class Region extends Model
{
    use Sluggable, Polymorphic;

    // Configuration

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable() : array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
    
    public function getRouteKeyName()
    {
        return 'slug';
    }
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Makers

    /**
     * [makeCache description]
     * @return RegionCache [description]
     */
    public function makeCache()
    {
        return App::make(RegionCache::class, ['region' => $this]);
    }
    
    /**
     * [makeRepo description]
     * @return RegionRepo [description]
     */
    public function makeRepo()
    {
        return App::make(RegionRepo::class, ['region' => $this]);
    }
}
