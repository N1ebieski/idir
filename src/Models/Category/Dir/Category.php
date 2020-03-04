<?php

namespace N1ebieski\IDir\Models\Category\Dir;

use N1ebieski\IDir\Models\Category\Category as BaseCategory;

/**
 * [Category description]
 */
class Category extends BaseCategory
{
    // Configuration

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'model_type' => 'N1ebieski\\IDir\\Models\\Dir',
        'status' => self::ACTIVE,
    ];

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return 'N1ebieski\\ICore\\Models\\Category\\Category';
    }

    // Relations

    /**
     * [morphs description]
     * @return [type] [description]
     */
    public function morphs()
    {
        return $this->morphedByMany('N1ebieski\IDir\Models\Dir', 'model', 'categories_models', 'category_id');
    }

    // Accessors

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute() : string
    {
        return 'dir';
    }

    // Loads

    /**
     * [loadNestedWithMorphsCount description]
     * @param  array    $filter    [description]
     * @return self [description]
     */
    public function loadNestedWithMorphsCountByFilter(array $filter) : self
    {
        return $this
            ->loadCount([
                'morphs' => function ($query) use ($filter) {
                    $query->active()->filterRegion($filter['region']);
                }
            ])
            ->load([
                'childrens' => function ($query) use ($filter) {
                    $query->active()
                        ->withCount([
                            'morphs' => function ($query) use ($filter) {
                                $query->active()->filterRegion($filter['region']);
                            }
                        ])
                        ->orderBy('position', 'asc');
                },
                'ancestors' => function ($query) use ($filter) {
                    $query->whereColumn('ancestor', '!=', 'descendant')
                        ->withCount([
                            'morphs' => function ($query) use ($filter) {
                                $query->active()->filterRegion($filter['region']);
                            }
                        ])
                        ->orderBy('depth', 'desc');
                }
            ]);
    }
}
