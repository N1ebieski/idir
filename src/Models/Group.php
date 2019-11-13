<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Traits\Carbonable;
use N1ebieski\ICore\Traits\Filterable;
use N1ebieski\ICore\Traits\FullTextSearchable;
use N1ebieski\ICore\Traits\Positionable;
use N1ebieski\IDir\Repositories\GroupRepo;
use N1ebieski\IDir\Services\GroupService;

/**
 * [Group description]
 */
class Group extends Model
{
    use Sluggable, Carbonable, Positionable, Filterable, FullTextSearchable;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'alt_id',
        'desc',
        'border',
        'position',
        'max_cats',
        'max_models',
        'max_models_daily',
        'visible',
        'apply_status',
        'url',
        'backlink'
    ];

    /**
     * The columns of the full text index
     *
     * @var array
     */
    protected $searchable = ['name'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'alt_id' => 1
    ];

    // Relations

    /**
     * [posts description]
     * @return [type] [description]
     */
    public function privileges()
    {
        return $this->belongsToMany('N1ebieski\IDir\Models\Privilege', 'groups_privileges');
    }

    /**
     * [prices description]
     * @return [type] [description]
     */
    public function prices()
    {
        return $this->hasMany('N1ebieski\IDir\Models\Price');
    }

    /**
     * [siblings description]
     * @return [type] [description]
     */
    public function siblings()
    {
        return $this;
    }

    // Overrides

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function(Group $group) {
            $group->position = $group->position ?? $group->getNextAfterLastPosition();
        });

        // Everytime the model is removed, we have to decrement siblings position by 1
        static::deleted(function(Group $group) {
            $group->decrementSiblings($group->position, null);
        });

        // Everytime the model's position
        // is changed, all siblings reordering will happen,
        // so they will always keep the proper order.
        static::saved(function(Group $group) {
            $group->reorderSiblings();
        });
    }

    // Scopes

    /**
     * [scopePublic description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopePublic(Builder $query) : Builder
    {
        return $query->where('visible', 1);
    }

    /**
     * [scopeObligatoryBacklink description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeObligatoryBacklink(Builder $query) : Builder
    {
        return $query->where('backlink', 2);
    }

    /**
     * [scopeFilterVisible description]
     * @param  Builder $query  [description]
     * @param  int|null  $visible [description]
     * @return Builder|null          [description]
     */
    public function scopeFilterVisible(Builder $query, int $visible = null) : ?Builder
    {
        return $query->when($visible !== null, function($query) use ($visible) {
            return $query->where('visible', $visible);
        });
    }

    // Makers

    /**
     * [makeRepo description]
     * @return GroupRepo [description]
     */
    public function makeRepo() : GroupRepo
    {
        return app()->make(GroupRepo::class, ['group' => $this]);
    }

    /**
     * [makeService description]
     * @return GroupService [description]
     */
    public function makeService() : GroupService
    {
        return app()->make(GroupService::class, ['group' => $this]);
    }
}
