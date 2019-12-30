<?php

namespace N1ebieski\IDir\Filters\Admin\Dir;

use N1ebieski\ICore\Filters\Filter;
use N1ebieski\ICore\Filters\Traits\HasSearch;
use N1ebieski\ICore\Filters\Traits\HasStatus;
use N1ebieski\IDir\Filters\Traits\HasGroup;
use N1ebieski\ICore\Filters\Traits\HasCategory;
use N1ebieski\ICore\Filters\Traits\HasReport;
use N1ebieski\ICore\Filters\Traits\HasAuthor;
use N1ebieski\ICore\Filters\Traits\HasOrderBy;
use N1ebieski\ICore\Filters\Traits\HasPaginate;

/**
 * [IndexFilter description]
 */
class IndexFilter extends Filter
{
    use HasSearch, HasStatus, HasGroup, HasCategory, HasReport, HasAuthor, HasOrderBy, HasPaginate;
}
