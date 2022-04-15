<?php

namespace N1ebieski\IDir\Models\Category\Dir;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use N1ebieski\IDir\Models\Category\Category as BaseCategory;
use N1ebieski\IDir\Database\Factories\Category\Dir\CategoryFactory;

class Category extends BaseCategory
{
    // Configuration

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'model_type' => \N1ebieski\IDir\Models\Dir::class,
        'status' => self::ACTIVE,
    ];

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return \N1ebieski\ICore\Models\Category\Category::class;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return CategoryFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\IDir\Database\Factories\Category\Dir\CategoryFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return MorphToMany
     */
    public function morphs(): MorphToMany
    {
        return $this->morphedByMany(\N1ebieski\IDir\Models\Dir::class, 'model', 'categories_models', 'category_id');
    }

    // Accessors

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute(): string
    {
        return 'dir';
    }

    // Loads

    /**
     * [loadNestedWithMorphsCount description]
     * @param  array    $filter    [description]
     * @return self [description]
     */
    public function loadNestedWithMorphsCountByFilter(array $filter): self
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

    // Factories

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return CategoryFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
