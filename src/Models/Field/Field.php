<?php

namespace N1ebieski\IDir\Models\Field;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Models\Traits\Polymorphic;
use N1ebieski\ICore\Models\Traits\Carbonable;
use N1ebieski\ICore\Models\Traits\Positionable;
use N1ebieski\ICore\Models\Traits\FullTextSearchable;
use N1ebieski\IDir\Models\Traits\Filterable;
use N1ebieski\IDir\Repositories\FieldRepo;
use N1ebieski\IDir\Services\FieldService;
use Illuminate\Database\Eloquent\Builder;

/**
 * [Field description]
 */
class Field extends Model
{
    use Polymorphic, Carbonable, Positionable, FullTextSearchable, Filterable;

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

    // Relations

    /**
     * [siblings description]
     * @return [type] [description]
     */
    public function siblings()
    {
        return $this->hasMany('N1ebieski\IDir\Models\Field\Field', 'model_type', 'model_type');
    }

    // Scopes

    /**
     * [scopeFilterType description]
     * @param  Builder $query [description]
     * @param  string|null  $type  [description]
     * @return Builder|null         [description]
     */
    public function scopeFilterType(Builder $query, string $type = null) : ?Builder
    {
        return $query->when($type !== null, function($query) use ($type) {
            return $query->where('type', $type);
        });
    }

    /**
     * [scopePublic description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopePublic(Builder $query) : Builder
    {
        return $query->where('visible', 1);
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

        static::saving(function(Field $field) {
            $field->position = $field->position ?? $field->getNextAfterLastPosition();
        });

        // Everytime the model is removed, we have to decrement siblings position by 1
        static::deleted(function(Field $field) {
            $field->decrementSiblings($field->position, null);
        });

        // Everytime the model's position
        // is changed, all siblings reordering will happen,
        // so they will always keep the proper order.
        static::saved(function(Field $field) {
            $field->reorderSiblings();
        });
    }

    // Accessors

    /**
     * [getOptionsAttribute description]
     * @return object [description]
     */
    public function getOptionsAttribute() : object
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
    public function setOptionsAttribute(array $value) : void
    {
        $this->attributes['options'] = json_encode($value);
    }

    // Checkers

    /**
     * [isNotDefault description]
     * @return bool [description]
     */
    public function isNotDefault() : bool
    {
        return !in_array($this->type, ['regions', 'map']);
    }

    // Makers

    /**
     * [makeRepo description]
     * @return FieldRepo [description]
     */
    public function makeRepo()
    {
        return app()->make(FieldRepo::class, ['field' => $this]);
    }

    /**
     * [makeService description]
     * @return FieldService [description]
     */
    public function makeService()
    {
        return app()->make(FieldService::class, ['field' => $this]);
    }
}
