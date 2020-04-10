<?php

namespace N1ebieski\IDir\Models;

use Carbon\Carbon;
use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Cache\DirCache;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Services\DirService;
use Cviebrock\EloquentTaggable\Taggable;
use N1ebieski\IDir\Repositories\DirRepo;
use Illuminate\Database\Eloquent\Builder;
use Cviebrock\EloquentSluggable\Sluggable;
use N1ebieski\IDir\Models\Traits\Filterable;
use N1ebieski\ICore\Models\Traits\Carbonable;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use N1ebieski\ICore\Models\Traits\FullTextSearchable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;

/**
 * [Dir description]
 */
class Dir extends Model
{
    use Sluggable, Taggable, FullTextSearchable, Filterable, Carbonable, PivotEventTrait;

    // Configuration

    /**
     * [public description]
     * @var int
     */
    public const ACTIVE = 1;

    /**
     * [public description]
     * @var int
     */
    public const INACTIVE = 0;

    /**
     * [public description]
     * @var int
     */
    public const PAYMENT_INACTIVE = 2;

    /**
     * [public description]
     * @var int
     */
    public const BACKLINK_INACTIVE = 3;

    /**
     * [public description]
     * @var int
     */
    public const STATUS_INACTIVE = 4;

    /**
     * [private description]
     * @var bool
     */
    private bool $pivotEvent = false;

    /**
     * [private description]
     * @var Group
     */
    protected $group;

    /**
     * [private description]
     * @var Payment
     */
    protected $payment;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
        'content_html',
        'status',
        'notes',
        'url',
        'privileged_at',
        'privileged_to'
    ];

    /**
     * The columns of the full text index
     *
     * @var array
     */
    protected $searchable = [
        'title',
        'content',
        'url'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => self::INACTIVE,
        'notes' => null,
        'privileged_at' => null,
        'privileged_to' => null
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'group_id' => 'integer',
        'user_id' => 'integer',
        'status' => 'integer',
        'privileged_at' => 'timestamp',
        'privileged_to' => 'timestamp',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp'
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    // Setters

    /**
     * @param Group $group
     *
     * @return static
     */
    public function setGroup(Group $group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * [setPayment description]
     * @param Payment $payment [description]
     */
    public function setPayment(Payment $payment)
    {
        $this->payment = $payment;

        return $this;
    }

    // Getters

    /**
     * [getGroup description]
     * @return Group [description]
     */
    public function getGroup() : Group
    {
        return $this->group;
    }

    /**
     * [getPayment description]
     * @return Payment|null [description]
     */
    public function getPayment() : ?Payment
    {
        return $this->payment;
    }

    // Overrides

    /**
     * Undocumented function
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::pivotUpdated(function ($model, $relationName, $pivotIds, $pivotIdsAttributes) {
            if ($model->pivotEvent === false && in_array($relationName, ['fields'])) {
                $model->fireModelEvent('updated');
                $model->pivotEvent = true;
            }
        });

        static::pivotAttached(function ($model, $relationName, $pivotIds, $pivotIdsAttributes) {
            if ($model->pivotEvent === false && in_array($relationName, ['fields', 'categories', 'tags', 'regions'])) {
                $model->fireModelEvent('updated');
                $model->pivotEvent = true;
            }
        });

        static::pivotDetached(function ($model, $relationName, $pivotIds) {
            if ($model->pivotEvent === false && in_array($relationName, ['fields', 'categories', 'tags', 'regions'])) {
                $model->fireModelEvent('updated');
                $model->pivotEvent = true;
            }
        });
    }

    /**
     * Override relacji tags, bo ma hardcodowane nazwy pól
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags(): MorphToMany
    {
        $model = config('taggable.model');
        return $this->morphToMany($model, 'model', 'tags_models', 'model_id', 'tag_id')
            ->withTimestamps();
    }

    // Relations

    /**
     * [user description]
     * @return [type] [description]
     */
    public function user()
    {
        return $this->belongsTo('N1ebieski\ICore\Models\User');
    }

    /**
     * [user description]
     * @return [type] [description]
     */
    public function group()
    {
        return $this->belongsTo('N1ebieski\IDir\Models\Group');
    }

    /**
     * [payments description]
     * @return [type] [description]
     */
    public function payments()
    {
        return $this->morphMany('N1ebieski\IDir\Models\Payment\Dir\Payment', 'model');
    }

    /**
     * [map description]
     * @return [type] [description]
     */
    public function map()
    {
        return $this->morphOne('N1ebieski\IDir\Models\Map\Map', 'model');
    }

    /**
     * [ratings description]
     * @return [type] [description]
     */
    public function ratings()
    {
        return $this->morphMany('N1ebieski\IDir\Models\Rating\Dir\Rating', 'model');
    }

    /**
     * [regions description]
     * @return [type] [description]
     */
    public function regions()
    {
        return $this->morphToMany(
            'N1ebieski\IDir\Models\Region\Region',
            'model',
            'regions_models',
            'model_id',
            'region_id'
        );
    }

    /**
     * [reports description]
     * @return [type] [description]
     */
    public function reports()
    {
        return $this->morphMany('N1ebieski\ICore\Models\Report\Report', 'model');
    }

    /**
     * [categories description]
     * @return [type] [description]
     */
    public function categories()
    {
        return $this->morphToMany(
            'N1ebieski\IDir\Models\Category\Dir\Category',
            'model',
            'categories_models',
            'model_id',
            'category_id'
        );
    }

    /**
     * [categories description]
     * @return [type] [description]
     */
    public function comments()
    {
        return $this->morphMany('N1ebieski\ICore\Models\Comment\Comment', 'model');
    }

    /**
     * [backlink description]
     * @return [type] [description]
     */
    public function backlink()
    {
        return $this->hasOne('N1ebieski\IDir\Models\DirBacklink');
    }

    /**
     * [status description]
     * @return [type] [description]
     */
    public function status()
    {
        return $this->hasOne('N1ebieski\IDir\Models\DirStatus');
    }

    /**
     * [fields description]
     * @return [type] [description]
     */
    public function fields()
    {
        return $this->morphToMany(
            'N1ebieski\IDir\Models\Field\Dir\Field',
            'model',
            'fields_values',
            'model_id',
            'field_id'
        )->withPivot('value');
    }

    // Scopes

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithAllPublicRels(Builder $query) : Builder
    {
        return $query->with([
            'group',
            'group.fields' => function ($query) {
                return $query->public();
            },
            'group.privileges',
            'fields',
            'regions',
            'categories',
            'tags',
            'user'
        ])
        ->withSumRating();
    }

    /**
     * [scopeWithSumRating description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeWithSumRating(Builder $query) : Builder
    {
        return $query->withCount([
            'ratings AS sum_rating' => function ($query) {
                $query->select(DB::raw('COALESCE(SUM(`ratings`.`rating`)/COUNT(*), 0) as `sum_rating`'));
            }
        ]);
    }

    /**
     * [scopeActive description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeActive(Builder $query) : Builder
    {
        return $query->where('dirs.status', static::ACTIVE);
    }

    /**
     * [scopeInactive description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeInactive(Builder $query) : Builder
    {
        return $query->where('dirs.status', static::INACTIVE);
    }

    /**
     * [scopePending description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopePending(Builder $query) : Builder
    {
        return $query->where('dirs.status', static::PAYMENT_INACTIVE);
    }

    /**
     * [scopeBacklinkInactive description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeBacklinkInactive(Builder $query) : Builder
    {
        return $query->where('dirs.status', static::BACKLINK_INACTIVE);
    }

    /**
     * [scopeActiveHasLinkPriviligeByComponent description]
     * @param  Builder $query     [description]
     * @param  array   $component [description]
     * @return Builder            [description]
     */
    public function scopeActiveHasLinkPriviligeByComponent(Builder $query, array $component) : Builder
    {
        return $query->selectRaw('id, url, title AS name, NULL')
            ->whereHas('group', function ($query) {
                $query->whereHas('privileges', function ($query) {
                    $query->where('name', 'place in the links component');
                });
            })
            ->join('categories_models', function ($query) use ($component) {
                $query->on('dirs.id', '=', 'categories_models.model_id')
                    ->whereIn('categories_models.category_id', $component['cats'])
                    ->where('categories_models.model_type', $this->getMorphClass());
            })
            // Rezygnuje z whereHas bo wydajność strasznie siada przy dużej ilości rekordów
            // ->whereIn('id', function ($query) use ($component) {
            //     $query->select('model_id')
            //         ->from('categories_models')
            //         ->whereIn('category_id', $component['cats'])
            //         ->where('model_type', get_class())
            //         ->get();
            // })
            // ->whereHas('categories', function ($query) use ($component) {
            //     $query->whereIn('categories.id', $component['cats']);
            // })
            ->where('url', '<>', null)
            ->active();
    }

    /**
     * [scopeFilterGroup description]
     * @param  Builder $query [description]
     * @param  Group|null  $group  [description]
     * @return Builder|null         [description]
     */
    public function scopeFilterGroup(Builder $query, Group $group = null) : ?Builder
    {
        return $query->when($group !== null, function ($query) use ($group) {
            $query->where('group_id', $group->id);
        });
    }

    // Accessors

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getSumRatingAttribute() : string
    {
        if (!isset($this->attributes['sum_rating'])) {
            $ratings = $this->getRelation('ratings');

            $sum_rating = $ratings->count() > 0 ? $ratings->sum('rating')/$ratings->count() : 0;

            return number_format($sum_rating, 2, '.', '');
        }

        return $this->attributes['sum_rating'];
    }

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliSelfAttribute() : string
    {
        return 'dir';
    }

    /**
     * Undocumented function
     *
    * @return string
     */
    public function getThumbnailUrlAttribute() : string
    {
        if (($cache['url'] = Config::get('idir.dir.thumbnail.cache.url'))
        && ($cache['key'] = Config::get('idir.dir.thumbnail.key'))) {
            return $this->makeCache()->rememberThumbnailUrl();
        }

        return Config::get('idir.dir.thumbnail.url').$this->url;
    }

    /**
     * [getHostAttribute description]
     * @return string        [description]
     */
    public function getUrlAsHostAttribute() : string
    {
        return parse_url($this->url, PHP_URL_HOST);
    }

    /**
     * [getPrivilegedToDiffAttribute description]
     * @return string [description]
     */
    public function getPrivilegedToDiffAttribute() : string
    {
        return Carbon::parse($this->privileged_to)->diffForHumans();
    }

    /**
     * Short content used in the listing
     * @return string [description]
     */
    public function getShortContentAttribute() : string
    {
        return mb_substr($this->content, 0, 500);
    }

    /**
     * [getTitleAsLinkAttribute description]
     * @return string [description]
     */
    public function getTitleAsLinkAttribute() : string
    {
        if ($this->url !== null) {
            $link = '<a ';

            if ($this->getRelation('group')->hasNoFollowPrivilege()) {
                $link .= 'rel="nofollow" ';
            }

            $link .= 'href="' . e($this->url) . '" target="_blank">' . e($this->title) . '</a>';
        }

        return $link ?? e($this->title);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getLinkAttribute() : string
    {
        if ($this->url !== null) {
            if ($this->getRelation('group')->hasDirectLinkPrivilege()) {
                return $this->title_as_link;
            }
        }

        return $this->link_as_html;
    }

    /**
     * [getContentHtmlAttribute description]
     * @return string [description]
     */
    public function getContentHtmlAttribute() : string
    {
        if ($this->getRelation('group')->hasEditorPrivilege()) {
            return Purifier::clean($this->attributes['content_html'], 'dir');
        }

        return strip_tags($this->attributes['content_html']);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getContentAsHtmlAttribute() : string
    {
        if (!$this->getRelation('group')->hasEditorPrivilege()) {
            return nl2br(e($this->content_html));
        }

        return $this->content_html;
    }

    /**
     * Content to the point of more link
     * @return string [description]
     */
    public function getLessContentHtmlAttribute() : string
    {
        return $this->short_content . '... <a href="' . URL::route('web.dir.show', [$this->slug])
        . '">' . Lang::get('idir::dirs.more') . '</a>';
    }

    /**
     * [getAttributesAttribute description]
     * @return array [description]
     */
    public function getAttributesAsArrayAttribute() : array
    {
        return $this->attributesToArray()
            + ['field' => $this->fields->keyBy('id')
                ->map(function ($item) {
                    return $item->decode_value;
                })
                ->toArray()]
            + ['categories' => $this->categories->pluck('id')->toArray()]
            + ['tags' => $this->tags->pluck('name')->toArray()];
    }

    /**
     * [getBacklinkAsHtmlAttribute description]
     * @return string [description]
     */
    public function getLinkAsHtmlAttribute() : string
    {
        $output = '<a href="' . route('web.dir.show', [$this->slug]) . '" title="' . $this->title . '">';
        $output .= e($this->title);
        $output .= '</a>';

        return $output;
    }

    // Checkers
    
    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isNulledPrivileges() : bool
    {
        return $this->privileged_at === null && $this->privileged_to === null;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isCommentable() : bool
    {
        return true;
    }

    /**
     * [isRenew description]
     * @return bool [description]
     */
    public function isRenew() : bool
    {
        return ($this->privileged_to !== null || $this->isPending())
            && $this->getRelation('group')->getRelation('prices')->isNotEmpty();
    }

    /**
     * [isGroup description]
     * @param  int  $id [description]
     * @return bool     [description]
     */
    public function isGroup(int $id) : bool
    {
        return $this->group_id === $id;
    }

    /**
     * [isUrl description]
     * @return bool [description]
     */
    public function isUrl() : bool
    {
        return $this->url !== null;
    }

    /**
     * [isPending description]
     * @return bool [description]
     */
    public function isPending() : bool
    {
        return $this->status === static::PAYMENT_INACTIVE;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isNotOk() : bool
    {
        return $this->status === static::STATUS_INACTIVE;
    }

    /**
     * [isActive description]
     * @return bool [description]
     */
    public function isActive() : bool
    {
        return $this->status === static::ACTIVE;
    }

    /**
     * [isPayment description]
     * @param  int  $id [description]
     * @return bool     [description]
     */
    public function isPayment(int $id) : bool
    {
        return !$this->isGroup($id) || $this->isPending();
    }

    /**
     * [isUpdateStatus description]
     * @return bool [description]
     */
    public function isUpdateStatus() : bool
    {
        return in_array($this->status, [
            static::INACTIVE,
            static::ACTIVE
        ]);
    }

    // Loads

    /**
     * [loadCheckoutPayments description]
     * @return self [description]
     */
    public function loadCheckoutPayments() : self
    {
        return $this->load(['payments' => function ($query) {
            $query->with('orderMorph')->where('status', static::INACTIVE);
        }]);
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public function loadAllPublicRels() : self
    {
        return $this->load([
                'fields',
                'categories',
                'group',
                'group.privileges',
                'group.fields' => function ($query) {
                    return $query->public();
                },
                'tags',
                'regions',
                'ratings'
            ]);
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public function loadAllRels() : self
    {
        return $this->load([
            'group',
            'group.privileges',
            'group.fields',
            'fields',
            'regions',
            'categories',
            'tags'
        ]);
    }

    // Mutators

    /**
     * [setContentAttribute description]
     * @param string $value [description]
     */
    public function setContentAttribute(string $value) : void
    {
        $this->attributes['content'] = strip_tags($value);
    }

    // Makers

    /**
     * [makeRepo description]
     * @return DirRepo [description]
     */
    public function makeRepo()
    {
        return App::make(DirRepo::class, ['dir' => $this]);
    }

    /**
     * [makeCache description]
     * @return DirCache [description]
     */
    public function makeCache()
    {
        return App::make(DirCache::class, ['dir' => $this]);
    }

    /**
     * [makeService description]
     * @return DirService [description]
     */
    public function makeService()
    {
        return App::make(DirService::class, ['dir' => $this]);
    }
}
