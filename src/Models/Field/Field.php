<?php

namespace N1ebieski\IDir\Models\Field;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\IDir\Repositories\FieldRepo;
use N1ebieski\IDir\ValueObjects\Field\Type;
use N1ebieski\IDir\Models\Traits\Filterable;
use N1ebieski\ICore\Models\Traits\Carbonable;
use N1ebieski\ICore\Models\Traits\Polymorphic;
use N1ebieski\IDir\ValueObjects\Field\Options;
use N1ebieski\IDir\ValueObjects\Field\Visible;
use N1ebieski\ICore\Models\Traits\Positionable;
use N1ebieski\IDir\Services\Field\FieldService;
use Illuminate\Database\Eloquent\Relations\HasMany;
use N1ebieski\ICore\Models\Traits\FullTextSearchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\IDir\Database\Factories\Field\FieldFactory;

/**
 * @property Type $type
 * @property Options $options
 * @property Visible $visible
 */
class Field extends Model
{
    use Polymorphic;
    use Carbonable;
    use Positionable;
    use FullTextSearchable;
    use Filterable;
    use HasFactory;

    // Configuration

    /**
     * The columns of the full text index
     *
     * @var array
     */
    protected $searchable = ['title'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fields';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'desc', 'type', 'options', 'position', 'visible'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'type' => \N1ebieski\IDir\Casts\Field\TypeCast::class,
        'options' => \N1ebieski\IDir\Casts\Field\OptionsCast::class,
        'visible' => \N1ebieski\IDir\Casts\Field\VisibleCast::class,
        'position' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return FieldFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\IDir\Database\Factories\Field\FieldFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return HasMany
     */
    public function siblings(): HasMany
    {
        return $this->hasMany(\N1ebieski\IDir\Models\Field\Field::class, 'model_type', 'model_type');
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

    // Accessors

    /**
     * [getDecodeValueAttribute description]
     * @return mixed [description]
     */
    public function getDecodeValueAttribute()
    {
        return json_decode($this->pivot->value);
    }

    // Factories

    /**
     * [makeRepo description]
     * @return FieldRepo [description]
     */
    public function makeRepo()
    {
        return App::make(FieldRepo::class, ['field' => $this]);
    }

    /**
     * [makeService description]
     * @return FieldService [description]
     */
    public function makeService()
    {
        return App::make(FieldService::class, ['field' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return FieldFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
