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
use Illuminate\Support\Facades\App;

/**
 * [Group description]
 */
class Group extends Model
{
    use Sluggable, Carbonable, Positionable, Filterable, FullTextSearchable;

    // Configuration

    /**
     * [public description]
     * @var int
     */
    public const VISIBLE = 1;

    /**
     * [public description]
     * @var int
     */
    public const INVISIBLE = 0;

    /**
     * [public description]
     * @var int
     */
    public const APPLY_ACTIVE = 1;

    /**
     * [public description]
     * @var int
     */
    public const APPLY_INACTIVE = 0;

    /**
     * [public description]
     * @var int
     */
    public const OPTIONAL_URL = 1;

    /**
     * [public description]
     * @var int
     */
    public const WITHOUT_URL = 0;

    /**
     * [public description]
     * @var int
     */
    public const OBLIGATORY_URL = 2;

    /**
     * [public description]
     * @var int
     */
    public const OPTIONAL_BACKLINK = 1;

    /**
     * [public description]
     * @var int
     */
    public const WITHOUT_BACKLINK = 0;

    /**
     * [public description]
     * @var int
     */
    public const OBLIGATORY_BACKLINK = 2;

    /**
     * [public description]
     * @var int
     */
    public const DEFAULT = 1;

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
    public function sluggable() : array
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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'alt_id' => 'integer',
        'max_cats' => 'integer',
        'max_models' => 'integer',
        'max_models_daily' => 'integer',
        'position' => 'integer',
        'visible' => 'integer',
        'apply_status' => 'integer',
        'url' => 'integer',
        'backlink' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
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
        return $this->morphToMany(
            'N1ebieski\IDir\Models\Field\Field',
            'model',
            'fields_models',
            'model_id',
            'field_id'
        );
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
    public function dirsToday()
    {
        return $this->hasMany('N1ebieski\IDir\Models\Dir')
            ->whereDate('created_at', '=', Carbon::now()->format('Y-m-d'))
            ->whereIn('status', [Dir::INACTIVE, Dir::ACTIVE]);
    }

    /**
     * Alternative group
     *
     * @return void
     */
    public function alt()
    {
        return $this->hasOne(static::class, 'id', 'alt_id');
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

        static::saving(function (Group $model) {
            $model->position = $model->position ?? $model->getNextAfterLastPosition();
        });

        // Everytime the model is removed, we have to decrement siblings position by 1
        static::deleted(function (Group $model) {
            $model->decrementSiblings($model->position, null);
        });

        // Everytime the model's position
        // is changed, all siblings reordering will happen,
        // so they will always keep the proper order.
        static::saved(function (Group $model) {
            $model->reorderSiblings();
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
        return $query->where('visible', static::VISIBLE);
    }

    /**
     * [scopeObligatoryBacklink description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeObligatoryBacklink(Builder $query) : Builder
    {
        return $query->where('backlink', static::OBLIGATORY_BACKLINK);
    }

    // Checkers

    /**
     * Undocumented function
     *
     * @param string $output
     * @return boolean
     */
    public function hasEditorPrivilege() : bool
    {
        return $this->privileges
            ->contains('name', 'additional options for editing content') ?
            true : false;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function hasDirectLinkPrivilege() : bool
    {
        return $this->privileges
            ->contains('name', 'direct link on listings') ?
            true : false;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function hasNoFollowPrivilege() : bool
    {
        return $this->privileges
            ->contains('name', 'direct link nofollow') ?
            true : false;
    }

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
        return $this->getAttribute('visible') === static::VISIBLE;
    }

    // Loads

    /**
     * [loadPublicFields description]
     * @return Group [description]
     */
    public function loadPublicFields() : Group
    {
        return $this->load([
            'fields' => function ($query) {
                $query->public();
            }
        ]);
    }

    // Makers

    /**
     * [makeRepo description]
     * @return GroupRepo [description]
     */
    public function makeRepo()
    {
        return App::make(GroupRepo::class, ['group' => $this]);
    }

    /**
     * [makeService description]
     * @return GroupService [description]
     */
    public function makeService()
    {
        return App::make(GroupService::class, ['group' => $this]);
    }
}
