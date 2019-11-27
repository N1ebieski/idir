<?php

namespace N1ebieski\IDir\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Models\Traits\Filterable as BaseFilterable;

/**
 * [trait description]
 */
trait Filterable
{
    use BaseFilterable;

    /**
     * [scopeFilterVisible description]
     * @param  Builder $query  [description]
     * @param  int|null  $visible [description]
     * @return Builder|null          [description]
     */
    public function scopeFilterVisible(Builder $query, int $visible = null) : ?Builder
    {
        return $query->when($visible !== null, function($query) use ($visible) {
            return $query->where('visible', $visible);
        });
    }
}
