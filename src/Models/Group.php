<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Models\Traits\Carbonable;
use N1ebieski\IDir\Models\Traits\Filterable;
use N1ebieski\ICore\Models\Traits\FullTextSearchable;
use N1ebieski\ICore\Models\Traits\Positionable;
use N1ebieski\IDir\Repositories\GroupRepo;
use N1ebieski\IDir\Services\GroupService;
use Carbon\Carbon;

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

    /**
     * [fields description]
     * @return [type] [description]
     */
    public function fields()
    {
        return $this->morphToMany('N1ebieski\IDir\Models\Field\Field', 'model', 'fields_models', 'model_id', 'field_id');
    }

    /**
     * [dirs description]
     * @return [type] [description]
     */
    public function dirs()
    {
        return $this->hasMany('N1ebieski\IDir\Models\Dir');
    }

    /**
     * [dirs description]
     * @return [type] [description]
     */
    public function dirs_today()
    {
        return $this->hasMany('N1ebieski\IDir\Models\Dir')->whereDate('created_at', '=', Carbon::now()->format('Y-m-d'));
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

    // Checkers

    /**
     * [isAvailable description]
     * @return bool [description]
     */
    public function isAvailable() : bool
    {
        $available = true;

        if ($this->max_models !== null && $this->dirs_count >= $this->max_models) {
            $available = false;
        }

        if ($this->max_models_daily !== null && $this->dirs_today_count >= $this->max_models_daily) {
            $available = false;
        }

        return $available;
    }

    /**
     * [isNotDefault description]
     * @return bool [description]
     */
    public function isNotDefault() : bool
    {
        return strtolower($this->name) !== 'default';
    }

    /**
     * [isPublic description]
     * @return bool [description]
     */
    public function isPublic() : bool
    {
        return $this->getAttribute('visible') === 1;
    }

    // Loads

    /**
     * [loadPublicFields description]
     * @return Group [description]
     */
    public function loadPublicFields() : Group
    {
        return $this->load([
            'fields' => function($query) {
                $query->public();
            }
        ]);
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
