<?php

namespace N1ebieski\IDir\Filters\Api\Group;

use N1ebieski\ICore\Filters\Filter;
use N1ebieski\ICore\Filters\Traits\HasExcept;
use N1ebieski\ICore\Filters\Traits\HasSearch;
use N1ebieski\IDir\Filters\Traits\HasVisible;
use N1ebieski\ICore\Filters\Traits\HasOrderBy;
use N1ebieski\ICore\Filters\Traits\HasPaginate;

class IndexFilter extends Filter
{
    use HasExcept;
    use HasSearch;
    use HasVisible;
    use HasOrderBy;
    use HasPaginate;
}
