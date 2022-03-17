<?php

namespace N1ebieski\IDir\Filters\Api\Auth\User;

use N1ebieski\ICore\Filters\Filter;
use N1ebieski\IDir\Filters\Traits\HasGroup;
use N1ebieski\ICore\Filters\Traits\HasExcept;
use N1ebieski\ICore\Filters\Traits\HasSearch;
use N1ebieski\ICore\Filters\Traits\HasStatus;
use N1ebieski\ICore\Filters\Traits\HasOrderBy;
use N1ebieski\ICore\Filters\Traits\HasPaginate;

class DirsFilter extends Filter
{
    use HasExcept;
    use HasSearch;
    use HasStatus;
    use HasGroup;
    use HasOrderBy;
    use HasPaginate;
}
