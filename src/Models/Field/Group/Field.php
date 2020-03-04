<?php

namespace N1ebieski\IDir\Models\Field\Group;

use N1ebieski\IDir\Models\Field\Field as BaseFieldModel;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\IDir\Models\Group;

/**
 * [Field description]
 */
class Field extends BaseFieldModel
{
    // Configuration

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'model_type' => 'N1ebieski\\IDir\\Models\\Group'
    ];

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return 'N1ebieski\\IDir\\Models\\Field\\Field';
    }

    // Accessors

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute() : string
    {
        return 'group';
    }

    /**
     * [getModelTypeAttribute description]
     * @return string [description]
     */
    public function getModelTypeAttribute()
    {
        return 'N1ebieski\\IDir\\Models\\Group';
    }

    // Relations

    /**
     * [morphs description]
     * @return [type] [description]
     */
    public function morphs()
    {
        return $this->morphedByMany('N1ebieski\IDir\Models\Group', 'model', 'fields_models');
    }

    // Scopes

    /**
     * [scopeFilterMorph description]
     * @param  Builder $query [description]
     * @param  Group|null  $morph  [description]
     * @return Builder|null         [description]
     */
    public function scopeFilterMorph(Builder $query, Group $morph = null) : ?Builder
    {
        return $query->when($morph !== null, function ($query) use ($morph) {
            $query->whereHas('morphs', function ($query) use ($morph) {
                $query->where('model_id', $morph->id);
            });
        });
    }
}
