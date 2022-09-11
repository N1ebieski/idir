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

namespace N1ebieski\IDir\Models\Category\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\ValueObjects\Category\Status;
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
        'status' => Status::ACTIVE,
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
                'morphs' => function (Builder|Dir $query) use ($filter) {
                    return $query->active()->filterRegion($filter['region']);
                }
            ])
            ->load([
                'childrens' => function (Builder|Category $query) use ($filter) {
                    return $query->active()
                        ->withCount([
                            'morphs' => function (Builder|Dir $query) use ($filter) {
                                return $query->active()->filterRegion($filter['region']);
                            }
                        ])
                        ->orderBy('position', 'asc');
                },
                'ancestors' => function (Builder|Category $query) use ($filter) {
                    return $query->whereColumn('ancestor', '!=', 'descendant')
                        ->withCount([
                            'morphs' => function (Builder|Dir $query) use ($filter) {
                                return $query->active()->filterRegion($filter['region']);
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
