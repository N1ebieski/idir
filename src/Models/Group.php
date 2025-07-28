<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use N1ebieski\IDir\Models\Field\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Cviebrock\EloquentSluggable\Sluggable;
use N1ebieski\IDir\Cache\Group\GroupCache;
use N1ebieski\IDir\ValueObjects\Group\Url;
use N1ebieski\IDir\ValueObjects\Group\Slug;
use N1ebieski\IDir\ValueObjects\Group\Visible;
use N1ebieski\IDir\Models\Traits\HasFilterable;
use N1ebieski\IDir\Services\Group\GroupService;
use N1ebieski\IDir\ValueObjects\Group\Backlink;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use N1ebieski\IDir\Repositories\Group\GroupRepo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use N1ebieski\ICore\Models\Traits\HasPositionable;
use N1ebieski\IDir\ValueObjects\Group\ApplyStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\IDir\Http\Resources\Group\GroupResource;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;
use N1ebieski\IDir\ValueObjects\Dir\Status as DirStatus;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use N1ebieski\IDir\Database\Factories\Group\GroupFactory;

/**
 * N1ebieski\IDir\Models\Group
 *
 * @property Slug $slug
 * @property Visible $visible
 * @property ApplyStatus $apply_status
 * @property Url $url
 * @property Backlink $backlink
 * @property string $color
 * @property int $id
 * @property int|null $alt_id
 * @property string $name
 * @property string|null $desc
 * @property string|null $border
 * @property int $max_cats
 * @property int|null $max_models
 * @property int|null $max_models_daily
 * @property int $position
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Group|null $alt
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\IDir\Models\Dir[] $dirs
 * @property-read int|null $dirs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\IDir\Models\Dir[] $dirsToday
 * @property-read int|null $dirs_today_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\IDir\Models\Field\Group\Field[] $fields
 * @property-read int|null $fields_count
 * @property-read string $created_at_diff
 * @property-read string $updated_at_diff
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\IDir\Models\Price[] $prices
 * @property-read int|null $prices_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\IDir\Models\Privilege[] $privileges
 * @property-read int|null $privileges_count
 * @method static Builder|Group exceptDefault()
 * @method static \N1ebieski\IDir\Database\Factories\Group\GroupFactory factory(...$parameters)
 * @method static Builder|Group filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static Builder|Group filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static Builder|Group filterExcept(?array $except = null)
 * @method static Builder|Group filterGroup(?\N1ebieski\IDir\Models\Group $group = null)
 * @method static Builder|Group filterOrderBy(?string $orderby = null)
 * @method static Builder|Group filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static Builder|Group filterRegion(?\N1ebieski\IDir\Models\Region\Region $region = null)
 * @method static Builder|Group filterReport(?int $report = null)
 * @method static Builder|Group filterSearch(?string $search = null)
 * @method static Builder|Group filterStatus(?int $status = null)
 * @method static Builder|Group filterType(?string $type = null)
 * @method static Builder|Group filterVisible(?int $visible = null)
 * @method static Builder|Group findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static Builder|Group newModelQuery()
 * @method static Builder|Group newQuery()
 * @method static Builder|Group obligatoryBacklink()
 * @method static Builder|Group orderBySearch(string $term)
 * @method static Builder|Group public()
 * @method static Builder|Group query()
 * @method static Builder|Group search(string $term)
 * @method static Builder|Group whereAltId($value)
 * @method static Builder|Group whereApplyStatus($value)
 * @method static Builder|Group whereBacklink($value)
 * @method static Builder|Group whereBorder($value)
 * @method static Builder|Group whereCreatedAt($value)
 * @method static Builder|Group whereDesc($value)
 * @method static Builder|Group whereId($value)
 * @method static Builder|Group whereMaxCats($value)
 * @method static Builder|Group whereMaxModels($value)
 * @method static Builder|Group whereMaxModelsDaily($value)
 * @method static Builder|Group whereName($value)
 * @method static Builder|Group wherePosition($value)
 * @method static Builder|Group whereSlug($value)
 * @method static Builder|Group whereUpdatedAt($value)
 * @method static Builder|Group whereUrl($value)
 * @method static Builder|Group whereVisible($value)
 * @method static Builder|Group withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 * @mixin \Eloquent
 */
class Group extends Model
{
    use Sluggable;
    use HasCarbonable;
    use HasPositionable;
    use HasFilterable;
    use HasFullTextSearchable;
    use HasFactory;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
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
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'slug' => \N1ebieski\IDir\Casts\Group\SlugCast::class,
        'alt_id' => 'integer',
        'max_cats' => 'integer',
        'max_models' => 'integer',
        'max_models_daily' => 'integer',
        'position' => 'integer',
        'visible' => \N1ebieski\IDir\Casts\Group\VisibleCast::class,
        'apply_status' => \N1ebieski\IDir\Casts\Group\ApplyStatusCast::class,
        'url' => \N1ebieski\IDir\Casts\Group\UrlCast::class,
        'backlink' => \N1ebieski\IDir\Casts\Group\BacklinkCast::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return GroupFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\IDir\Database\Factories\Group\GroupFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return BelongsToMany
     */
    public function privileges(): BelongsToMany
    {
        return $this->belongsToMany(\N1ebieski\IDir\Models\Privilege::class, 'groups_privileges');
    }

    /**
     * Undocumented function
     *
     * @return HasMany
     */
    public function prices(): HasMany
    {
        return $this->hasMany(\N1ebieski\IDir\Models\Price::class);
    }

    /**
     *
     * @return self
     */
    public function siblings(): self
    {
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return MorphToMany
     */
    public function fields(): MorphToMany
    {
        return $this->morphToMany(
            \N1ebieski\IDir\Models\Field\Group\Field::class,
            'model',
            'fields_models',
            'model_id',
            'field_id'
        );
    }

    /**
     * Undocumented function
     *
     * @return HasMany
     */
    public function dirs(): HasMany
    {
        return $this->hasMany(\N1ebieski\IDir\Models\Dir::class);
    }

    /**
     * Undocumented function
     *
     * @return HasMany
     */
    public function dirsToday(): HasMany
    {
        return $this->hasMany(\N1ebieski\IDir\Models\Dir::class)
            ->whereDate('created_at', '=', Carbon::now()->format('Y-m-d'))
            ->whereIn('status', [DirStatus::INACTIVE, DirStatus::ACTIVE]);
    }

    /**
     * Undocumented function
     *
     * @return HasOne
     */
    public function alt(): HasOne
    {
        return $this->hasOne(static::class, 'id', 'alt_id');
    }

    // Scopes

    /**
     * [scopePublic description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('visible', Visible::ACTIVE);
    }

    /**
     * [scopeObligatoryBacklink description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeObligatoryBacklink(Builder $query): Builder
    {
        return $query->where('backlink', Backlink::ACTIVE);
    }

    /**
     * [scopePublic description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeExceptDefault(Builder $query): Builder
    {
        return $query->where('slug', '<>', Slug::DEFAULT);
    }

    // Checkers

    /**
     *
     * @return bool
     */
    public function hasEditorPrivilege(): bool
    {
        return $this->privileges->contains('name', 'additional options for editing content');
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function hasDirectLinkPrivilege(): bool
    {
        return $this->privileges->contains('name', 'direct link on listings');
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function hasNoFollowPrivilege(): bool
    {
        return $this->privileges->contains('name', 'direct link nofollow');
    }

    public function hasGenerateContentPrivilege(): bool
    {
        return $this->privileges->contains('name', 'generate content by AI');
    }

    /**
     * [isAvailable description]
     * @return bool [description]
     */
    public function isAvailable(): bool
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

    // Loads

    /**
     * [loadPublicFields description]
     * @return Group [description]
     */
    public function loadPublicFields(): Group
    {
        return $this->load([
            'fields' => function (MorphToMany|Builder|Field $query) {
                return $query->public();
            }
        ]);
    }

    // Factories

    /**
     * [makeRepo description]
     * @return GroupRepo [description]
     */
    public function makeRepo()
    {
        return App::make(GroupRepo::class, ['group' => $this]);
    }

    /**
     * [makeCache description]
     * @return GroupCache [description]
     */
    public function makeCache()
    {
        return App::make(GroupCache::class, ['group' => $this]);
    }

    /**
     * [makeService description]
     * @return GroupService [description]
     */
    public function makeService()
    {
        return App::make(GroupService::class, ['group' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return GroupFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }

    /**
     * [makeResource description]
     * @return GroupResource [description]
     */
    public function makeResource()
    {
        return App::make(GroupResource::class, ['group' => $this]);
    }
}
