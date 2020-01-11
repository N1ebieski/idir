<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentTaggable\Taggable;
use N1ebieski\ICore\Models\Traits\Carbonable;
use N1ebieski\ICore\Models\Traits\FullTextSearchable;
use N1ebieski\ICore\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use N1ebieski\ICore\Services\TagService;
use N1ebieski\IDir\Services\DirService;
use N1ebieski\IDir\Cache\DirCache;
use N1ebieski\IDir\Repositories\DirRepo;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use Carbon\Carbon;

/**
 * [Dir description]
 */
class Dir extends Model
{
    use Sluggable, Taggable, FullTextSearchable, Filterable, Carbonable;

    // Configuration

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
        'status' => 0,
        'notes' => null,
        'privileged_at' => null,
        'privileged_to' => null
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
    * Override metody z paczki Taggable bo ma hardcodowane nazwy tabel w SQL
     *
     * @param int|null $limit
     * @param int $minCount
     *
     * @return array
     */
    public function popularTags(int $limit = null, int $minCount = 1): array
    {
        $tags = app(TagService::class)->getPopularTags($limit, static::class, $minCount);

        return $tags->shuffle()->all();
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
     * [ratings description]
     * @return [type] [description]
     */
    public function ratings()
    {
        return $this->morphMany('N1ebieski\ICore\Models\Rating\Rating', 'model');
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
        return $this->morphToMany('N1ebieski\IDir\Models\Category\Dir\Category', 'model', 'categories_models', 'model_id', 'category_id');
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
     * [fields description]
     * @return [type] [description]
     */
    public function fields()
    {
        return $this->morphToMany('N1ebieski\IDir\Models\Field\Dir\Field', 'model', 'fields_values', 'model_id', 'field_id')
            ->withPivot('value');
    }

    // Scopes

    /**
     * [scopeWithSumRating description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeWithSumRating(Builder $query) : Builder
    {
        return $query->selectRaw('dirs.*, COALESCE(SUM(ratings.rating), 0) AS sum_rating')
            ->leftJoin('ratings', function($q) {
                 $q->on('ratings.model_id', '=', 'dirs.id');
                 $q->where('ratings.model_type', '=', 'N1ebieski\IDir\Models\Dir');
            })
            ->groupBy('dirs.id');
    }

    /**
     * [scopeActive description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeActive(Builder $query) : Builder
    {
        return $query->where('dirs.status', 1);
    }

    /**
     * [scopeInactive description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeInactive(Builder $query) : Builder
    {
        return $query->where('dirs.status', 0);
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
            ->whereHas('group', function($query) {
                $query->whereHas('privileges', function($query) {
                    $query->where('name', 'place in the links component');
                });
            })->whereHas('categories', function ($query) use ($component) {
                $query->whereIn('id', $component['cats']);
            })->where('url', '<>', null)
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
        return $query->when($group !== null, function($query) use ($group) {
            $query->where('group_id', $group->id);
        });
    }

    // Accessors

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
        if (($cache['url'] = config('idir.dir.thumbnail.cache.url')) 
        && ($cache['key'] = config('idir.dir.thumbnail.key'))) {
            return $this->makeCache()->rememberThumbnailUrl();
        }

        return config('idir.dir.thumbnail.url').$this->url;
    }

    // /**
    //  * [getHostAttribute description]
    //  * @return string        [description]
    //  */
    // public function getUrlHostAttribute() : string
    // {
    //     return parse_url($this->url, PHP_URL_HOST);
    // }

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
        return substr($this->content, 0, 500);
    }

    /**
     * [getTitleAsLinkAttribute description]
     * @return string [description]
     */
    public function getTitleAsLinkAttribute() : string
    {
        return $this->url !== null ?
            '<a href="' . $this->url . '" target="_blank">' . $this->title . '</a>'
            : $this->title;
    }

    /**
     * [getAttributesAttribute description]
     * @return array [description]
     */
    public function getAttributesAsArrayAttribute() : array
    {
        return $this->getAttributes()
            + ['field' => $this->fields->keyBy('id')
                ->map(function($item) {
                    return $item->decode_value;
                })
                ->toArray()]
            + ['categories' => $this->categories->pluck('id')->toArray()];
    }

    /**
     * [getBacklinkAsHtmlAttribute description]
     * @return string [description]
     */
    public function getLinkAsHtmlAttribute() : string
    {
        $output = '<a href="' . route('web.dir.show', [$this->slug]) . '" title="' . $this->title . '">';
        $output .= $this->title;
        $output .= '</a>';

        return $output;
    }

    // Checkers

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
     * [isPending description]
     * @return bool [description]
     */
    public function isPending() : bool
    {
        return $this->status === 2;
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
        return in_array($this->status, [0, 1]);
    }

    // Loads

    /**
     * [loadCheckoutPayments description]
     * @return self [description]
     */
    public function loadCheckoutPayments() : self
    {
        return $this->load(['payments' => function($query) {
            $query->with('price_morph')->where('status', 0);
        }]);
    }

    // /**
    //  * [loadGroupWithRels description]
    //  * @return self [description]
    //  */
    // public function loadGroupWithRels() : self
    // {
    //     return $this->load([
    //         'group',
    //         'group.privileges',
    //         'group.fields' => function($query) {
    //             return $query->public();
    //         }
    //     ]);
    // }

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
        return app()->make(DirRepo::class, ['dir' => $this]);
    }

    /**
     * [makeCache description]
     * @return DirCache [description]
     */
    public function makeCache()
    {
        return app()->make(DirCache::class, ['dir' => $this]);
    }

    /**
     * [makeService description]
     * @return DirService [description]
     */
    public function makeService()
    {
        return app()->make(DirService::class, ['dir' => $this]);
    }
}
