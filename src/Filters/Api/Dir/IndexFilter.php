<?php

namespace N1ebieski\IDir\Filters\Api\Dir;

use N1ebieski\ICore\Filters\Filter;
use N1ebieski\IDir\Filters\Traits\HasGroup;
use N1ebieski\ICore\Filters\Traits\HasAuthor;
use N1ebieski\ICore\Filters\Traits\HasExcept;
use N1ebieski\ICore\Filters\Traits\HasReport;
use N1ebieski\ICore\Filters\Traits\HasSearch;
use N1ebieski\ICore\Filters\Traits\HasStatus;
use N1ebieski\ICore\Filters\Traits\HasOrderBy;
use N1ebieski\ICore\Filters\Traits\HasCategory;
use N1ebieski\ICore\Filters\Traits\HasPaginate;

class IndexFilter extends Filter
{
    use HasExcept;
    use HasSearch;
    use HasStatus;
    use HasGroup;
    use HasCategory;
    use HasReport;
    use HasAuthor;
    use HasOrderBy;
    use HasPaginate;
}