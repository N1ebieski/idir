<?php

namespace N1ebieski\IDir\Models\Field\Group;

use N1ebieski\IDir\Models\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use N1ebieski\IDir\Models\Field\Field as BaseFieldModel;
use N1ebieski\IDir\Database\Factories\Field\Group\FieldFactory;

class Field extends BaseFieldModel
{
    // Configuration

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'model_type' => \N1ebieski\IDir\Models\Group::class
    ];

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return \N1ebieski\IDir\Models\Field\Field::class;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return FieldFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\IDir\Database\Factories\Field\Group\FieldFactory::new();
    }

    // Accessors

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute(): string
    {
        return 'group';
    }

    /**
     * [getModelTypeAttribute description]
     * @return string [description]
     */
    public function getModelTypeAttribute()
    {
        return \N1ebieski\IDir\Models\Group::class;
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return MorphToMany
     */
    public function morphs(): MorphToMany
    {
        return $this->morphedByMany(\N1ebieski\IDir\Models\Group::class, 'model', 'fields_models');
    }

    // Scopes

    /**
     * [scopeFilterMorph description]
     * @param  Builder $query [description]
     * @param  Group|null  $morph  [description]
     * @return Builder|null         [description]
     */
    public function scopeFilterMorph(Builder $query, Group $morph = null): ?Builder
    {
        return $query->when($morph !== null, function ($query) use ($morph) {
            $query->whereHas('morphs', function ($query) use ($morph) {
                $query->where('model_id', $morph->id);
            });
        });
    }

    // Factories

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
