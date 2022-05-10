<?php

namespace N1ebieski\IDir\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Config;
use N1ebieski\IDir\Cache\Dir\DirCache;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentTaggable\Taggable;
use N1ebieski\ICore\Utils\MigrationUtil;
use Illuminate\Database\Eloquent\Builder;
use Cviebrock\EloquentSluggable\Sluggable;
use N1ebieski\IDir\Services\Dir\DirService;
use N1ebieski\IDir\Repositories\Dir\DirRepo;
use Illuminate\Support\Collection as Collect;
use N1ebieski\IDir\Models\Traits\HasFilterable;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use N1ebieski\ICore\Models\Traits\HasStatFilterable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use N1ebieski\IDir\Database\Factories\Dir\DirFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use N1ebieski\ICore\ValueObjects\Stat\Slug as StatSlug;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;
use N1ebieski\IDir\ValueObjects\Dir\Status as DirStatus;
use N1ebieski\IDir\ValueObjects\Dir\Comment as DirComment;
use N1ebieski\IDir\ValueObjects\Payment\Status as PaymentStatus;

/**
 * @property DirStatus $status
 * @property DirComment $comment
 * @property \N1ebieski\IDir\ValueObjects\Dir\Url $url
 * @property \N1ebieski\IDir\Models\Group $group
 */
class Dir extends Model
{
    use Sluggable;
    use Taggable;
    use HasFullTextSearchable;
    use HasCarbonable;
    use PivotEventTrait;
    use HasFactory;
    use HasFilterable, HasStatFilterable {
        HasStatFilterable::scopeFilterOrderBy insteadof HasFilterable;
    }

    // Configuration

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
    public $searchable = [
        'url',
        'title',
        'content',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => DirStatus::INACTIVE,
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
        'status' => \N1ebieski\IDir\Casts\Dir\StatusCast::class,
        'url' => \N1ebieski\IDir\Casts\Dir\UrlCast::class,
        'privileged_at' => 'datetime',
        'privileged_to' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return DirFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\IDir\Database\Factories\Dir\DirFactory::new();
    }

    // Overrides

    /**
     * Override relacji tags, bo ma hardcodowane nazwy pól
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags(): MorphToMany
    {
        $model = Config::get('taggable.model');

        return $this->morphToMany($model, 'model', 'tags_models', 'model_id', 'tag_id')
            ->withTimestamps();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\N1ebieski\IDir\Models\User::class);
    }

    /**
     * Undocumented function
     *
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(\N1ebieski\IDir\Models\Group::class);
    }

    /**
     * Undocumented function
     *
     * @return MorphMany
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(\N1ebieski\IDir\Models\Payment\Dir\Payment::class, 'model');
    }

    /**
     * Undocumented function
     *
     * @return MorphOne
     */
    public function map(): MorphOne
    {
        return $this->morphOne(\N1ebieski\IDir\Models\Map\Map::class, 'model');
    }

    /**
     * Undocumented function
     *
     * @return MorphMany
     */
    public function ratings(): MorphMany
    {
        return $this->morphMany(\N1ebieski\IDir\Models\Rating\Dir\Rating::class, 'model');
    }

    /**
     * Undocumented function
     *
     * @return MorphToMany
     */
    public function regions(): MorphToMany
    {
        return $this->morphToMany(
            \N1ebieski\IDir\Models\Region\Region::class,
            'model',
            'regions_models',
            'model_id',
            'region_id'
        );
    }

    /**
     * Undocumented function
     *
     * @return MorphMany
     */
    public function reports(): MorphMany
    {
        return $this->morphMany(\N1ebieski\ICore\Models\Report\Report::class, 'model');
    }

    /**
     * Undocumented function
     *
     * @return MorphToMany
     */
    public function categories(): MorphToMany
    {
        return $this->morphToMany(
            \N1ebieski\IDir\Models\Category\Dir\Category::class,
            'model',
            'categories_models',
            'model_id',
            'category_id'
        );
    }

    /**
     * Undocumented function
     *
     * @return MorphMany
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(\N1ebieski\ICore\Models\Comment\Comment::class, 'model');
    }

    /**
     * Undocumented function
     *
     * @return HasOne
     */
    public function backlink(): HasOne
    {
        return $this->hasOne(\N1ebieski\IDir\Models\DirBacklink::class);
    }

    /**
     * Undocumented function
     *
     * @return HasOne
     */
    public function status(): HasOne
    {
        return $this->hasOne(\N1ebieski\IDir\Models\DirStatus::class);
    }

    /**
     * Undocumented function
     *
     * @return MorphToMany
     */
    public function fields(): MorphToMany
    {
        return $this->morphToMany(
            \N1ebieski\IDir\Models\Field\Dir\Field::class,
            'model',
            'fields_values',
            'model_id',
            'field_id'
        )->withPivot('value');
    }

    /**
     * Undocumented function
     *
     * @return MorphToMany
     */
    public function stats(): MorphToMany
    {
        return $this->morphToMany(
            \N1ebieski\IDir\Models\Stat\Dir\Stat::class,
            'model',
            'stats_values',
            'model_id',
            'stat_id'
        )->withPivot('value');
    }

    // Scopes

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithAllRels(Builder $query): Builder
    {
        return $query->with([
            'group',
            'group.prices',
            'group.fields' => function ($query) {
                $query->orderBy('position', 'asc');
            },
            'group.privileges',
            'fields',
            'regions',
            'categories',
            'tags',
            'user',
            'payments',
            'payments.group',
            'backlink',
            'status'
        ])
        ->when(App::make(MigrationUtil::class)->contains('create_stats_table'), function ($query) {
            $query->with('stats');
        })
        ->withSumRating();
    }

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithAllPublicRels(Builder $query): Builder
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
        ->when(App::make(MigrationUtil::class)->contains('create_stats_table'), function ($query) {
            $query->with('stats');
        })
        ->withSumRating();
    }

    /**
     * [scopeWithSumRating description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeWithSumRating(Builder $query): Builder
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
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('dirs.status', DirStatus::ACTIVE);
    }

    /**
     * [scopeInactive description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('dirs.status', DirStatus::INACTIVE);
    }

    /**
     * [scopePending description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('dirs.status', DirStatus::PAYMENT_INACTIVE);
    }

    /**
     * [scopeBacklinkInactive description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeBacklinkInactive(Builder $query): Builder
    {
        return $query->where('dirs.status', DirStatus::BACKLINK_INACTIVE);
    }

    /**
     * [scopeActiveHasLinkPriviligeByComponent description]
     * @param  Builder $query     [description]
     * @param  array   $component [description]
     * @return Builder            [description]
     */
    public function scopeActiveHasLinkPriviligeByComponent(Builder $query, array $component): Builder
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
            ->where('url', '<>', null)
            ->active();
    }

    // Accessors

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getSumRatingAttribute(): string
    {
        if (!isset($this->attributes['sum_rating'])) {
            $ratings = $this->getRelation('ratings');

            $sum_rating = $ratings->count() > 0 ? $ratings->sum('rating') / $ratings->count() : 0;

            return number_format($sum_rating, 2, '.', '');
        }

        return $this->attributes['sum_rating'];
    }

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliSelfAttribute(): string
    {
        return 'dir';
    }

    /**
     * Undocumented function
     *
    * @return string
     */
    public function getThumbnailUrlAttribute(): string
    {
        if (
            ($cache['url'] = Config::get('idir.dir.thumbnail.cache.url'))
            && ($cache['key'] = Config::get('idir.dir.thumbnail.key'))
        ) {
            return $this->makeCache()->rememberThumbnailUrl();
        }

        return Config::get('idir.dir.thumbnail.url') . $this->url;
    }

    /**
     * [getPrivilegedToDiffAttribute description]
     * @return string [description]
     */
    public function getPrivilegedToDiffAttribute(): string
    {
        return Carbon::parse($this->privileged_to)->diffForHumans(['parts' => 2]);
    }

    /**
     * Short content used in the listing
     * @return string [description]
     */
    public function getShortContentAttribute(): string
    {
        return mb_substr(
            e($this->content, false),
            0,
            Config::get('idir.dir.short_content')
        );
    }

    /**
     * [getTitleAsLinkAttribute description]
     * @return string [description]
     */
    public function getTitleAsLinkAttribute(): string
    {
        if ($this->url->isUrl()) {
            $link = '<a rel="noopener';

            if ($this->group->hasNoFollowPrivilege()) {
                $link .= ' nofollow';
            }

            $link .= '" target="_blank" title="' . e($this->title) . '" ';

            if (App::make(MigrationUtil::class)->contains('create_stats_table')) {
                $link .= 'class="click-stat" data-route="' . URL::route('web.stat.dir.click', [StatSlug::CLICK, $this->slug]) . '" ';
            }

            $link .= 'href="' . e($this->url) . '">' . e($this->title) . '</a>';
        }

        return $link ?? e($this->title);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getUrlAsLinkAttribute(): ?string
    {
        if ($this->url->isUrl()) {
            $link = '<a rel="noopener';

            if ($this->getRelation('group')->hasNoFollowPrivilege()) {
                $link .= ' nofollow';
            }

            $link .= '" target="_blank" title="' . e($this->title) . '" ';

            if (App::make(MigrationUtil::class)->contains('create_stats_table')) {
                $link .= 'class="click-stat" data-route="' . URL::route('web.stat.dir.click', [StatSlug::CLICK, $this->slug]) . '" ';
            }

            $link .= 'href="' . e($this->url) . '">' . e($this->url->getHost()) . '</a>';
        }

        return $link ?? null;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getLinkAttribute(): string
    {
        if ($this->isUrl()) {
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
    public function getContentHtmlAttribute(): string
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
    public function getContentAsHtmlAttribute(): string
    {
        if (!$this->getRelation('group')->hasEditorPrivilege()) {
            return nl2br(e($this->content_html, false));
        }

        return $this->content_html;
    }

    /**
     * Content to the point of more link
     * @return string [description]
     */
    public function getLessContentHtmlAttribute(): string
    {
        return $this->short_content . '... <a href="' . URL::route('web.dir.show', [$this->slug])
        . '">' . Lang::get('idir::dirs.more') . '</a>';
    }

    /**
     * [getAttributesAttribute description]
     * @return array [description]
     */
    public function getAttributesAsArrayAttribute(): array
    {
        return $this->attributesToArray()
            + ['field' => $this->fields->keyBy('id')
                ->map(function ($item) {
                    if ($item->type === 'map') {
                        return Collect::make($item->decode_value)->map(function ($item) {
                            $item = (array)$item;

                            return $item;
                        })->toArray();
                    }

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
    public function getLinkAsHtmlAttribute(): string
    {
        $output = '<a href="' . route('web.dir.show', [$this->slug]) . '" title="' . e($this->title) . '">';
        $output .= e($this->title);
        $output .= '</a>';

        return $output;
    }

    /**
     *
     * @return DirComment
     */
    public function getCommentAttribute(): DirComment
    {
        return DirComment::active();
    }

    // Checkers

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isNulledPrivileges(): bool
    {
        return $this->privileged_at === null && $this->privileged_to === null;
    }

    /**
     * [isRenew description]
     * @return bool [description]
     */
    public function isRenew(): bool
    {
        return ($this->privileged_to !== null || $this->status->isPaymentInactive())
            && $this->getRelation('group')->getRelation('prices')->isNotEmpty();
    }

    /**
     * Undocumented function
     *
     * @param GroupId $id
     * @return boolean
     */
    public function isPayment(int $id): bool
    {
        return $this->group_id !== $id || $this->status->isPaymentInactive();
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isUrl(): bool
    {
        return !$this->getRelation('group')->url->isInactive() && $this->url->isUrl();
    }

    // Loads

    /**
     * [loadCheckoutPayments description]
     * @return self [description]
     */
    public function loadCheckoutPayments(): self
    {
        return $this->load(['payments' => function ($query) {
            $query->with('orderMorph')->where('status', PaymentStatus::UNFINISHED);
        }]);
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public function loadAllPublicRels(): self
    {
        return $this->load(array_filter([
                'fields',
                'categories' => function ($query) {
                    return $query->withAncestorsExceptSelf();
                },
                'group',
                'group.privileges',
                'group.fields' => function ($query) {
                    return $query->orderBy('position', 'asc')
                        ->public();
                },
                'tags',
                'regions',
                'ratings',
                App::make(MigrationUtil::class)->contains('create_stats_table') ?
                    'stats' : null
            ]));
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public function loadAllRels(): self
    {
        return $this->load(array_filter([
            'group',
            'group.privileges',
            'group.fields',
            'fields',
            'regions',
            'ratings',
            'categories' => function ($query) {
                return $query->withAncestorsExceptSelf();
            },
            'tags',
            'status',
            'backlink',
            App::make(MigrationUtil::class)->contains('create_stats_table') ?
                'stats' : null
        ]));
    }

    // Mutators

    /**
     * [setContentAttribute description]
     * @param string $value [description]
     */
    public function setContentAttribute(string $value): void
    {
        $this->attributes['content'] = !empty($value) ? strip_tags($value) : null;
    }

    // Factories

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

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return DirFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
