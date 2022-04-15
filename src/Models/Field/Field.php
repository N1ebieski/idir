<?php

namespace N1ebieski\IDir\Models\Field;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\IDir\Repositories\FieldRepo;
use N1ebieski\IDir\Models\Traits\Filterable;
use N1ebieski\ICore\Models\Traits\Carbonable;
use N1ebieski\ICore\Models\Traits\Polymorphic;
use N1ebieski\ICore\Models\Traits\Positionable;
use N1ebieski\IDir\Services\Field\FieldService;
use Illuminate\Database\Eloquent\Relations\HasMany;
use N1ebieski\ICore\Models\Traits\FullTextSearchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\IDir\Database\Factories\Field\FieldFactory;

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
    public const REQUIRED = 1;

    /**
     * [public description]
     * @var int
     */
    public const OPTIONAL = 0;

    /**
     * [public description]
     * @var array
     */
    public const DEFAULT = ['regions', 'map', 'gus'];

    /**
     * [public description]
     * @var array
     */
    public const AVAILABLE = ['input', 'textarea', 'select', 'multiselect', 'checkbox', 'image'];

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
        'visible' => 'integer',
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
        return $query->where('visible', static::VISIBLE);
    }

    // Accessors

    /**
     * [getOptionsAttribute description]
     * @return object [description]
     */
    public function getOptionsAttribute(): object
    {
        $options = json_decode($this->attributes['options']);

        if (isset($options->options)) {
            $options->options_as_string = implode("\r\n", $options->options);
        }

        return $options;
    }

    /**
     * [getDecodeValueAttribute description]
     * @return mixed [description]
     */
    public function getDecodeValueAttribute()
    {
        return json_decode($this->pivot->value);
    }

    // Mutators

    /**
     * [setOptionsAttribute description]
     * @param array $value [description]
     */
    public function setOptionsAttribute(array $value): void
    {
        $this->attributes['options'] = json_encode($value);
    }

    // Checkers

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isPublic(): bool
    {
        return $this->getAttribute('visible') === static::VISIBLE;
    }

    /**
     * [isNotDefault description]
     * @return bool [description]
     */
    public function isNotDefault(): bool
    {
        return !in_array($this->type, static::DEFAULT);
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isRequired(): bool
    {
        return (int)$this->options->required === static::REQUIRED;
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
